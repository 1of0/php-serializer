<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Visitors;

use OneOfZero\Json\Nodes\AbstractNode;
use OneOfZero\Json\Nodes\ArrayNode;
use OneOfZero\Json\Nodes\MemberNode;
use OneOfZero\Json\Nodes\ObjectNode;
use OneOfZero\Json\Exceptions\ReferenceException;
use OneOfZero\Json\Exceptions\ResumeSerializationException;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Helpers\Metadata;
use OneOfZero\Json\ReferableInterface;
use ReflectionClass;
use stdClass;

class SerializingVisitor extends AbstractVisitor
{
	/**
	 * @param mixed $value
	 * @param AbstractNode|null $parent
	 *
	 * @return mixed
	 *
	 * @throws SerializationException
	 */
	public function visit($value, AbstractNode $parent = null)
	{
		if (is_object($value) && $value instanceof stdClass)
		{
			// stdClass objects are weirdos
			$value = (array)$value;
		}

		if (is_array($value))
		{
			$valueContext = (new ArrayNode)
				->withArray($value)
				->withParent($parent)
			;

			return $this->visitArray($valueContext)->getSerializedArray();
		}

		if (is_object($value))
		{
			$class = $this->proxyHelper->getClassBeneath($value);

			if ($this->proxyHelper->isProxy($value))
			{
				$value = $this->proxyHelper->unproxy($value);
			}
			
			$valueMapper = $this->mapperFactory->mapObject(new ReflectionClass($class));

			$valueContext = (new ObjectNode)
				->withReflector($valueMapper->getTarget())
				->withMapper($valueMapper)
				->withInstance($value)
				->withParent($parent)
			;

			return $this->visitObject($valueContext)->getSerializedInstance();
		}

		return $value;
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
		foreach ($context->getArray() as $key => $value)
		{
			if ($value === null)
			{
				$context = $context->withSerializedArrayValue(null, $key);
			}
			
			$context = $context->withSerializedArrayValue($this->visit($value), $key);
		}

		return $context;
	}

	/**
	 * @param ObjectNode $context
	 *
	 * @return ObjectNode|null
	 *
	 * @throws SerializationException
	 */
	protected function visitObject(ObjectNode $context)
	{
		$mapper = $context->getMapper();

		if ($context->getInstance() === null)
		{
			return null;
		}

		if (!$mapper->wantsNoMetadata())
		{
			$context = $context->withMetadata(Metadata::TYPE, $context->getReflector()->name);
		}

		if ($mapper->hasSerializingConverter())
		{
			$converter = $this->resolveObjectConverter($mapper->getSerializingConverterType());

			try
			{
				return $context->withSerializedInstance($converter->serialize($context));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		foreach ($mapper->getMembers() as $memberMapper)
		{
			$memberContext = (new MemberNode)
				->withValue($memberMapper->getValue($context->getInstance()))
				->withReflector($memberMapper->getTarget())
				->withMapper($memberMapper)
				->withParent($context)
			;

			if ($result = $this->visitObjectMember($memberContext))
			{
				$context = $context->withSerializedInstanceMember($memberMapper->getName(), $result->getSerializedValue());
			}
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
		$value = $context->getValue();

		if (!$mapper->isIncluded() || !$mapper->isSerializable())
		{
			return null;
		}

		if ($mapper->hasSerializingConverter())
		{
			$converter = $this->resolveMemberConverter($mapper->getSerializingConverterType());

			try
			{
				return $context->withSerializedValue($converter->serialize($context));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if ($value === null)
		{
			return $this->configuration->includeNullValues ? $context : null;
		}

		if ($mapper->isReference())
		{
			return $context->withSerializedValue($this->createReference($context));
		}

		return $context->withSerializedValue($this->visit($value));
	}

	/**
	 * @param MemberNode $context
	 *
	 * @return array|null
	 *
	 * @throws ReferenceException
	 */
	protected function createReference(MemberNode $context)
	{
		if ($context->getMapper()->isArray())
		{
			return $this->createReferenceArray($context);
		}

		return $this->createReferenceItem($context, $context->getValue());
	}

	/**
	 * @param MemberNode $context
	 *
	 * @return array
	 *
	 * @throws ReferenceException
	 */
	protected function createReferenceArray(MemberNode $context)
	{
		if (!is_array($context->getValue()))
		{
			throw new ReferenceException("Property {$context->getReflector()->name} in class {$context->getParent()->getReflector()->name} is marked as an array, but does not hold an array");
		}

		$references = [];
		foreach ($context->getValue() as $item)
		{
			$references[] = $this->createReferenceItem($context, $item);
		}
		return $references;
	}

	/**
	 * @param MemberNode $context
	 * @param mixed $value
	 *
	 * @return array|null
	 *
	 * @throws ReferenceException
	 */
	protected function createReferenceItem(MemberNode $context, $value)
	{
		$type = $this->getType($context, $value);

		if (!($value instanceof ReferableInterface))
		{
			throw new ReferenceException("Property {$context->getReflector()->name} in class {$context->getParent()->getReflector()->name} is marked as a reference, but does not implement ReferableInterface");
		}

		if ($type === null)
		{
			throw new ReferenceException("Property {$context->getReflector()->name} in class {$context->getParent()->getReflector()->name} is marked as a reference, but does not specify or imply a valid type");
		}

		$reference = [];
		Metadata::set($reference, Metadata::TYPE, $type);
		Metadata::set($reference, Metadata::ID, $value->getId());
		return $reference;
	}

	/**
	 * @param MemberNode $context
	 * @param mixed $value
	 *
	 * @return null|string
	 */
	protected function getType(MemberNode $context, $value)
	{
		if ($context->getMapper()->getType() !== null)
		{
			return $context->getMapper()->getType();
		}
		elseif (is_object($value))
		{
			return get_class($value);
		}
		else
		{
			return null;
		}
	}
}
