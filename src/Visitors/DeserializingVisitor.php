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
				$objectNode = (new AnonymousObjectNode)
					->withInstance(new stdClass())
					->withSerializedInstance($serializedValue)
					->withParent($parent)
				;

				return $this->visitAnonymousObject($objectNode)->getInstance();
			}

			$objectReflector = new ReflectionClass($type);

			$object = $this->containerHas($type)
				? $this->containerGet($type)
				: $objectReflector->newInstanceWithoutConstructor()
			;

			$objectNode = (new ObjectNode)
				->withReflector($objectReflector)
				->withMapper($this->mapperFactory->mapObject($objectReflector))
				->withInstance($object)
				->withSerializedInstance($serializedValue)
				->withParent($parent)
			;

			return $this->visitObject($objectNode)->getInstance();
		}

		if (is_array($serializedValue))
		{
			$valueNode = (new ArrayNode)
				->withArray([])
				->withSerializedArray($serializedValue)
				->withParent($parent)
			;

			return $this->visitArray($valueNode)->getArray();
		}

		return $serializedValue;
	}

	/**
	 * @param ArrayNode $node
	 *
	 * @return ArrayNode
	 *
	 * @throws SerializationException
	 */
	protected function visitArray(ArrayNode $node)
	{
		foreach ($node->getSerializedArray() as $key => $value)
		{
			if ($value === null)
			{
				$node = $node->withArrayValue(null, $key);
			}
			
			$node = $node->withArrayValue($this->visit($value), $key);
		}

		return $node;
	}
	
	/**
	 * @param AnonymousObjectNode $node
	 *
	 * @return AnonymousObjectNode
	 */
	protected function visitAnonymousObject(AnonymousObjectNode $node)
	{
		foreach ($node->getSerializedInstance() as $key => $value)
		{
			$node = $node->withInstanceMember($key, $this->visit($value));
		}

		return $node;
	}

	/**
	 * @param ObjectNode $node
	 *
	 * @return ObjectNode
	 *
	 * @throws SerializationException
	 */
	protected function visitObject(ObjectNode $node)
	{
		$mapper = $node->getMapper();

		if ($mapper->hasDeserializingConverter())
		{
			$converter = $this->resolveObjectConverter($mapper->getDeserializingConverterType());

			try
			{
				return $node->withInstance($converter->deserialize($node));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		foreach ($mapper->getMembers() as $memberMapper)
		{
			$serializedValue = $node->getSerializedMemberValue($memberMapper->getName());
			
			$memberNode = (new MemberNode)
				->withSerializedValue($serializedValue)
				->withReflector($memberMapper->getTarget())
				->withMapper($memberMapper)
				->withParent($node)
			;

			$node = $node->withInstanceMember($this->visitObjectMember($memberNode));
		}
		
		return $node;
	}

	/**
	 * @param MemberNode $node
	 *
	 * @return MemberNode|null
	 *
	 * @throws SerializationException
	 */
	protected function visitObjectMember(MemberNode $node)
	{
		$mapper = $node->getMapper();

		if (!$mapper->isIncluded() || !$mapper->isDeserializable())
		{
			return $node;
		}

		if ($mapper->hasDeserializingConverter())
		{
			$converter = $this->resolveMemberConverter($mapper->getDeserializingConverterType());

			try
			{
				return $node->withValue($converter->deserialize($node));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if ($mapper->isReference())
		{
			return $node->withValue($this->resolveReference($node));
		}

		return $node->withValue($this->visit($node->getSerializedValue(), $node, $node->getMapper()->getType()));
	}

	/**
	 * @param MemberNode $node
	 *
	 * @return ReferableInterface
	 */
	protected function resolveReference(MemberNode $node)
	{
		if (is_array($node->getSerializedValue()))
		{
			return $this->resolveReferenceArray($node);
		}

		return $this->resolveReferenceItem($node, $node->getSerializedValue());
	}

	/**
	 * @param MemberNode $node
	 *
	 * @return ReferableInterface[]
	 */
	protected function resolveReferenceArray(MemberNode $node)
	{
		$resolved = [];

		foreach ($node->getSerializedValue() as $item)
		{
			$resolved[] = $this->resolveReferenceItem($node, $item);
		}

		return $resolved;
	}

	/**
	 * @param MemberNode $node
	 * @param mixed $item
	 *
	 * @return ReferableInterface
	 *
	 * @throws ReferenceException
	 */
	protected function resolveReferenceItem(MemberNode $node, $item)
	{
		if (!$this->referenceResolver)
		{
			throw new ReferenceException("No reference resolver configured");
		}

		$id = Metadata::get($item, Metadata::ID);
		$type = $this->getType($item, $node);

		if ($type === null)
		{
			throw new ReferenceException("Property {$node->getReflector()->name} in class {$node->getParent()->getReflector()->name} is marked as a reference, but does not specify or imply a valid type");
		}

		if ($id === null)
		{
			throw new ReferenceException("Property {$node->getReflector()->name} in class {$node->getParent()->getReflector()->name} is marked as a reference, but the serialized data does not contain a valid reference");
		}

		return $this->referenceResolver->resolve($type, $id, $node->getMapper()->isReferenceLazy());
	}

	/**
	 * @param stdClass $serializedValue
	 * @param MemberNode|null $node
	 * @param string|null $typeHint
	 *
	 * @return null|string
	 * @throws MissingTypeException
	 */
	protected function getType($serializedValue, $node = null, $typeHint = null)
	{
		if ($typeHint === null && Metadata::contains($serializedValue, Metadata::TYPE))
		{
			// Type hint is not explicitly provided, try to retrieve it from the serialized value's metadata
			$typeHint = Metadata::get($serializedValue, Metadata::TYPE);
		}

		if ($typeHint === null && $node instanceof MemberNode)
		{
			$typeHint = $node->getMapper()->getType();
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
