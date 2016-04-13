<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Visitors;

use OneOfZero\Json\Enums\OnRecursion;
use OneOfZero\Json\Exceptions\ConverterException;
use OneOfZero\Json\Exceptions\NotSupportedException;
use OneOfZero\Json\Exceptions\RecursionException;
use OneOfZero\Json\Exceptions\SkipMemberException;
use OneOfZero\Json\Mappers\MemberMapperInterface;
use OneOfZero\Json\Mappers\ObjectMapperInterface;
use OneOfZero\Json\Nodes\AbstractNode;
use OneOfZero\Json\Nodes\AbstractObjectNode;
use OneOfZero\Json\Nodes\AnonymousObjectNode;
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
		if (is_array($value))
		{
			$valueNode = (new ArrayNode)
				->withArray($value)
				->withParent($parent)
			;

			return $this->visitArray($valueNode)->getSerializedArray();
		}
		
		if (is_object($value) && $value instanceof stdClass)
		{
			$objectNode = AnonymousObjectNode
				::fromInstance($value)
				->withParent($parent)
			;

			return $this->visitObject($objectNode)->getSerializedInstance();
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
	 * @param AbstractObjectNode $node
	 *
	 * @return AbstractObjectNode|null
	 *
	 * @throws SerializationException
	 */
	protected function visitObject(AbstractObjectNode $node)
	{
		/** @var ObjectNode $node */
		
		if ($this->hasContractResolver)
		{
			$node = $node->withMapper($this->createContractObjectMapper($node));
		}
		
		$mapper = $node->getMapper();


		if ($node instanceof ObjectNode && !$mapper->isMetadataDisabled())
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

		if ($node->getDepth() >= $this->configuration->maxDepth)
		{
			return $this->handleMaxDepth($node);
		}

		foreach ($node->getMapper()->getMembers() as $memberMapper)
		{
			$memberNode = (new MemberNode)
				->withValue($memberMapper->getValue($node->getInstance()))
				->withReflector($memberMapper->getTarget())
				->withMapper($memberMapper)
				->withParent($node)
			;

			try
			{
				$memberNode = $this->visitObjectMember($memberNode);
				
				$memberName = $memberNode->getMapper()->getSerializedName();
				$memberValue = $memberNode->getSerializedValue();

				if ($memberValue !== null || $this->configuration->includeNullValues)
				{
					$node = $node->withSerializedInstanceMember($memberName, $memberValue);
				}
			}
			catch (SkipMemberException $e)
			{
			}
		}
		
		return $node;
	}

	/**
	 * @param MemberNode $node
	 * 
	 * @return MemberNode
	 * 
	 * @throws SkipMemberException
	 * @throws ConverterException
	 */
	protected function visitObjectMember(MemberNode $node)
	{
		/** @var MemberNode $node */
		
		if ($this->hasContractResolver)
		{
			$node = $node->withMapper($this->createContractMemberMapper($node));
		}
		
		$mapper = $node->getMapper();

		if (!$mapper->isIncluded() || !$mapper->isSerializable())
		{
			throw new SkipMemberException();
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

		if ($node->getValue() !== null)
		{
			if ($mapper->isReference())
			{
				$node = $node->withSerializedValue($this->createReference($node));
			}
			else
			{
				$node = $node->withSerializedValue($this->visit($node->getValue(), $node));
			}
		}
		
		return $node;
	}

	/**
	 * @param AbstractObjectNode $node
	 *
	 * @return ObjectMapperInterface
	 */
	protected function createContractObjectMapper(AbstractObjectNode $node)
	{
		$mapper = $this->configuration->contractResolver->createSerializingObjectContract($node);
		$mapper->setBase($node->getMapper());
		$mapper->setTarget($node->getMapper()->getTarget());
		return $mapper;
	}

	/**
	 * @param MemberNode $node
	 *
	 * @return MemberMapperInterface
	 */
	protected function createContractMemberMapper(MemberNode $node)
	{
		$mapper = $this->configuration->contractResolver->createSerializingMemberContract($node);
		$mapper->setBase($node->getMapper());
		$mapper->setTarget($node->getMapper()->getTarget());
		$mapper->setMemberParent($this->createContractObjectMapper($node->getParent()));

		return $mapper;
	}

	/**
	 * @param AbstractObjectNode $node
	 *
	 * @return AbstractObjectNode
	 *
	 * @throws NotSupportedException
	 * @throws RecursionException
	 * @throws ReferenceException
	 * @throws ResumeSerializationException
	 */
	protected function handleRecursion(AbstractObjectNode $node)
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
				$type = ($node instanceof ObjectNode) ? $node->getReflector()->name : 'stdClass';
				throw new RecursionException("Infinite recursion detected for class {$type}");
				
			default:
				throw new NotSupportedException('The configured default recursion handling strategy is unknown or unsupported');
		}
	}

	/**
	 * @param ObjectNode $node
	 * 
	 * @return ObjectNode
	 * 
	 * @throws NotSupportedException
	 * @throws RecursionException
	 */
	protected function handleMaxDepth(ObjectNode $node)
	{
		switch ($this->configuration->defaultMaxDepthHandlingStrategy)
		{
			case OnRecursion::SET_NULL:
				return $node->withSerializedInstance(null);

			case OnRecursion::THROW_EXCEPTION:
				throw new RecursionException('Hit maximum configured recursion depth');

			default:
				throw new NotSupportedException('The configured default handling strategy for maximum depth is unknown or unsupported');
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
	 * @param AbstractObjectNode $node
	 *
	 * @return AbstractObjectNode
	 *
	 * @throws ReferenceException
	 */
	protected function createObjectReference(AbstractObjectNode $node)
	{
		if ($node->getInstance() === null)
		{
			return $node->withSerializedInstance(null);
		}

		$type = get_class($node->getInstance());

		if (!($node->getInstance() instanceof ReferableInterface))
		{
			throw new ReferenceException("An instance of {$type} exists as a recursively used instance. The configuration specifies to create references of recursive objects, but {$type} does not implement ReferableInterface");
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
