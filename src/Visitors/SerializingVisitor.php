<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Visitors;

use OneOfZero\Json\Enums\OnRecursion;
use OneOfZero\Json\Exceptions\NotSupportedException;
use OneOfZero\Json\Exceptions\RecursionException;
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
			$valueNode = (new ArrayNode)
				->withArray($value)
				->withParent($parent)
			;

			return $this->visitArray($valueNode)->getSerializedArray();
		}

		if (is_object($value))
		{
			$class = $this->proxyHelper->getClassBeneath($value);

			if ($this->proxyHelper->isProxy($value))
			{
				$value = $this->proxyHelper->unproxy($value);
			}
			
			$valueMapper = $this->mapperFactory->mapObject(new ReflectionClass($class));

			$valueNode = (new ObjectNode)
				->withReflector($valueMapper->getTarget())
				->withMapper($valueMapper)
				->withInstance($value)
				->withParent($parent)
			;

			return $this->visitObject($valueNode)->getSerializedInstance();
		}

		return $value;
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
		foreach ($node->getArray() as $key => $value)
		{
			if ($value === null)
			{
				$node = $node->withSerializedArrayValue(null, $key);
			}
			
			$node = $node->withSerializedArrayValue($this->visit($value), $key);
		}

		return $node;
	}

	/**
	 * @param ObjectNode $node
	 *
	 * @return ObjectNode|null
	 *
	 * @throws SerializationException
	 */
	protected function visitObject(ObjectNode $node)
	{
		$mapper = $node->getMapper();

		if ($node->getInstance() === null)
		{
			return $node->withSerializedInstance(null);
		}

		if ($node->isRecursiveInstance())
		{
			try
			{
				return $this->handleRecursion($node);
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if (!$mapper->wantsNoMetadata())
		{
			$node = $node->withMetadata(Metadata::TYPE, $node->getReflector()->name);
		}

		if ($mapper->hasSerializingConverter())
		{
			$converter = $this->resolveObjectConverter($mapper->getSerializingConverterType());

			try
			{
				return $node->withSerializedInstance($converter->serialize($node));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		foreach ($mapper->getMembers() as $memberMapper)
		{
			$memberNode = (new MemberNode)
				->withValue($memberMapper->getValue($node->getInstance()))
				->withReflector($memberMapper->getTarget())
				->withMapper($memberMapper)
				->withParent($node)
			;

			if ($result = $this->visitObjectMember($memberNode))
			{
				$node = $node->withSerializedInstanceMember($memberMapper->getName(), $result->getSerializedValue());
			}
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
		$value = $node->getValue();

		if (!$mapper->isIncluded() || !$mapper->isSerializable())
		{
			return null;
		}

		if ($mapper->hasSerializingConverter())
		{
			$converter = $this->resolveMemberConverter($mapper->getSerializingConverterType());

			try
			{
				return $node->withSerializedValue($converter->serialize($node));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if ($value === null)
		{
			return $this->configuration->includeNullValues ? $node : null;
		}

		if ($mapper->isReference())
		{
			return $node->withSerializedValue($this->createReference($node));
		}

		return $node->withSerializedValue($this->visit($value, $node));
	}

	/**
	 * @param ObjectNode $node
	 *
	 * @return ObjectNode
	 *
	 * @throws NotSupportedException
	 * @throws RecursionException
	 * @throws ReferenceException
	 * @throws ResumeSerializationException
	 */
	protected function handleRecursion(ObjectNode $node)
	{
		switch ($this->configuration->defaultRecursionHandlingStrategy)
		{
			case OnRecursion::CONTINUE_MAPPING:
				throw new ResumeSerializationException();

			case OnRecursion::SET_NULL:
				return $node->withSerializedInstance(null);

			case OnRecursion::CREATE_REFERENCE:
				return $this->createObjectReference($node);

			case OnRecursion::THROW_EXCEPTION:
				throw new RecursionException();
				
			default:
				throw new NotSupportedException('The configured default recursion handling strategy is unknown or unsupported');
		}
	}

	/**
	 * @param MemberNode $node
	 *
	 * @return array|null
	 *
	 * @throws ReferenceException
	 */
	protected function createReference(MemberNode $node)
	{
		if ($node->getMapper()->isArray())
		{
			return $this->createReferenceArray($node);
		}

		return $this->createReferenceItem($node, $node->getValue());
	}

	/**
	 * @param MemberNode $node
	 *
	 * @return array
	 *
	 * @throws ReferenceException
	 */
	protected function createReferenceArray(MemberNode $node)
	{
		if (!is_array($node->getValue()))
		{
			throw new ReferenceException("Property {$node->getReflector()->name} in class {$node->getParent()->getReflector()->name} is marked as an array, but does not hold an array");
		}

		$references = [];
		foreach ($node->getValue() as $item)
		{
			$references[] = $this->createReferenceItem($node, $item);
		}
		return $references;
	}

	/**
	 * @param MemberNode $node
	 * @param mixed $value
	 *
	 * @return array|null
	 *
	 * @throws ReferenceException
	 */
	protected function createReferenceItem(MemberNode $node, $value)
	{
		$type = $this->getType($value, $node);

		if (!($value instanceof ReferableInterface))
		{
			throw new ReferenceException("Property {$node->getReflector()->name} in class {$node->getParent()->getReflector()->name} is marked as a reference, but does not implement ReferableInterface");
		}

		if ($type === null)
		{
			throw new ReferenceException("Property {$node->getReflector()->name} in class {$node->getParent()->getReflector()->name} is marked as a reference, but does not specify or imply a valid type");
		}

		$reference = [];
		Metadata::set($reference, Metadata::TYPE, $type);
		Metadata::set($reference, Metadata::ID, $value->getId());
		return $reference;
	}

	/**
	 * @param ObjectNode $node
	 *
	 * @return ObjectNode
	 *
	 * @throws ReferenceException
	 */
	protected function createObjectReference(ObjectNode $node)
	{
		if ($node->getInstance() === null)
		{
			return $node->withSerializedInstance(null);
		}

		$type = get_class($node->getInstance());

		if (!($node->getInstance() instanceof ReferableInterface))
		{
			throw new ReferenceException("An instance of {$node->getReflector()->name} exists as a recursively used instance. The configuration specifies to create references of recursive objects, but {$node->getReflector()->name} does not implement ReferableInterface");
		}

		$reference = [];
		Metadata::set($reference, Metadata::TYPE, $type);
		Metadata::set($reference, Metadata::ID, $node->getInstance()->getId());

		return $node->withSerializedInstance($reference);
	}

	/**
	 * @param mixed $value
	 * @param MemberNode $node
	 *
	 * @return null|string
	 */
	protected function getType($value, MemberNode $node)
	{
		if ($node->getMapper()->getType() !== null)
		{
			return $node->getMapper()->getType();
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
