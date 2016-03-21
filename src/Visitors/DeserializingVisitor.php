<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Visitors;

use OneOfZero\Json\Nodes\AbstractNode;
use OneOfZero\Json\Nodes\AnonymousObjectNode;
use OneOfZero\Json\Nodes\ArrayNode;
use OneOfZero\Json\Nodes\MemberNode;
use OneOfZero\Json\Nodes\ObjectNode;
use OneOfZero\Json\Exceptions\MissingTypeException;
use OneOfZero\Json\Exceptions\ReferenceException;
use OneOfZero\Json\Exceptions\ResumeSerializationException;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Helpers\Metadata;
use OneOfZero\Json\ReferableInterface;
use ReflectionClass;
use stdClass;

class DeserializingVisitor extends AbstractVisitor
{
	/**
	 * @param mixed $serializedValue
	 * @param AbstractNode|null $parent
	 * @param string|null $typeHint
	 *
	 * @return mixed
	 *
	 * @throws SerializationException
	 */
	public function visit($serializedValue, AbstractNode $parent = null, $typeHint = null)
	{
		if (is_object($serializedValue))
		{
			$type = $this->getType($serializedValue, $parent, $typeHint);

			if ($type === null)
			{
				// Type not resolved, deserialize as anonymous object
				$objectContext = (new AnonymousObjectNode)
					->withInstance(new stdClass())
					->withSerializedInstance($serializedValue)
					->withParent($parent)
				;

				return $this->visitAnonymousObject($objectContext)->getInstance();
			}

			$objectReflector = new ReflectionClass($type);

			$object = $this->containerHas($type)
				? $this->containerGet($type)
				: $objectReflector->newInstanceWithoutConstructor()
			;

			$objectContext = (new ObjectNode)
				->withReflector($objectReflector)
				->withMapper($this->mapperFactory->mapObject($objectReflector))
				->withInstance($object)
				->withSerializedInstance($serializedValue)
				->withParent($parent)
			;

			return $this->visitObject($objectContext)->getInstance();
		}

		if (is_array($serializedValue))
		{
			$valueContext = (new ArrayNode)
				->withArray([])
				->withSerializedArray($serializedValue)
				->withParent($parent)
			;

			return $this->visitArray($valueContext)->getArray();
		}

		return $serializedValue;
	}

	/**
	 * @param ArrayNode $context
	 *
	 * @return ArrayNode
	 *
	 * @throws SerializationException
	 */
	protected function visitArray(ArrayNode $context)
	{
		foreach ($context->getSerializedArray() as $key => $value)
		{
			if ($value === null)
			{
				$context = $context->withArrayValue(null, $key);
			}
			
			$context = $context->withArrayValue($this->visit($value), $key);
		}

		return $context;
	}
	
	/**
	 * @param AnonymousObjectNode $context
	 *
	 * @return AnonymousObjectNode
	 */
	protected function visitAnonymousObject(AnonymousObjectNode $context)
	{
		foreach ($context->getSerializedInstance() as $key => $value)
		{
			$context = $context->withInstanceMember($key, $this->visit($value));
		}

		return $context;
	}

	/**
	 * @param ObjectNode $context
	 *
	 * @return ObjectNode
	 *
	 * @throws SerializationException
	 */
	protected function visitObject(ObjectNode $context)
	{
		$mapper = $context->getMapper();

		if ($mapper->hasDeserializingConverter())
		{
			$converter = $this->resolveObjectConverter($mapper->getDeserializingConverterType());

			try
			{
				return $context->withInstance($converter->deserialize($context));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		foreach ($mapper->getMembers() as $memberMapper)
		{
			$serializedValue = $context->getSerializedMemberValue($memberMapper->getName());
			
			$memberContext = (new MemberNode)
				->withSerializedValue($serializedValue)
				->withReflector($memberMapper->getTarget())
				->withMapper($memberMapper)
				->withParent($context)
			;

			$context = $context->withInstanceMember($this->visitObjectMember($memberContext));
		}
		
		return $context;
	}

	/**
	 * @param MemberNode $context
	 *
	 * @return MemberNode|null
	 *
	 * @throws SerializationException
	 */
	protected function visitObjectMember(MemberNode $context)
	{
		$mapper = $context->getMapper();

		if (!$mapper->isIncluded() || !$mapper->isDeserializable())
		{
			return $context;
		}

		if ($mapper->hasDeserializingConverter())
		{
			$converter = $this->resolveMemberConverter($mapper->getDeserializingConverterType());

			try
			{
				return $context->withValue($converter->deserialize($context));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if ($mapper->isReference())
		{
			return $context->withValue($this->resolveReference($context));
		}

		return $context->withValue($this->visit($context->getSerializedValue(), $context, $context->getMapper()->getType()));
	}

	/**
	 * @param MemberNode $context
	 *
	 * @return ReferableInterface
	 */
	protected function resolveReference(MemberNode $context)
	{
		if (is_array($context->getSerializedValue()))
		{
			return $this->resolveReferenceArray($context);
		}

		return $this->resolveReferenceItem($context, $context->getSerializedValue());
	}

	/**
	 * @param MemberNode $context
	 *
	 * @return ReferableInterface[]
	 */
	protected function resolveReferenceArray(MemberNode $context)
	{
		$resolved = [];

		foreach ($context->getSerializedValue() as $item)
		{
			$resolved[] = $this->resolveReferenceItem($context, $item);
		}

		return $resolved;
	}

	/**
	 * @param MemberNode $context
	 * @param mixed $item
	 *
	 * @return ReferableInterface
	 *
	 * @throws ReferenceException
	 */
	protected function resolveReferenceItem(MemberNode $context, $item)
	{
		if (!$this->referenceResolver)
		{
			throw new ReferenceException("No reference resolver configured");
		}

		$id = Metadata::get($item, Metadata::ID);
		$type = $this->getType($item, $context);

		if ($type === null)
		{
			throw new ReferenceException("Property {$context->getReflector()->name} in class {$context->getParent()->getReflector()->name} is marked as a reference, but does not specify or imply a valid type");
		}

		if ($id === null)
		{
			throw new ReferenceException("Property {$context->getReflector()->name} in class {$context->getParent()->getReflector()->name} is marked as a reference, but the serialized data does not contain a valid reference");
		}

		return $this->referenceResolver->resolve($type, $id, $context->getMapper()->isReferenceLazy());
	}

	/**
	 * @param stdClass $serializedValue
	 * @param MemberNode|null $context
	 * @param string|null $typeHint
	 *
	 * @return null|string
	 * @throws MissingTypeException
	 */
	protected function getType($serializedValue, $context = null, $typeHint = null)
	{
		if ($typeHint === null && Metadata::contains($serializedValue, Metadata::TYPE))
		{
			// Type hint is not explicitly provided, try to retrieve it from the serialized value's metadata
			$typeHint = Metadata::get($serializedValue, Metadata::TYPE);
		}

		if ($typeHint === null && $context instanceof MemberNode)
		{
			$typeHint = $context->getMapper()->getType();
		}

		if ($typeHint !== null && !class_exists($typeHint))
		{
			// Type hint does not exist
			if ($this->configuration->strictTypeResolution)
			{
				throw new MissingTypeException("Cannot resolve type $typeHint");
			}

			$typeHint = null;
		}

		return $typeHint;
	}
}
