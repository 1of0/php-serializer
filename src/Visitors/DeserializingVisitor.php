<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Visitors;

use OneOfZero\Json\Helpers\ObjectHelper;
use OneOfZero\Json\Mappers\MemberMapperInterface;
use OneOfZero\Json\Mappers\ObjectMapperInterface;
use OneOfZero\Json\Nodes\AbstractNode;
use OneOfZero\Json\Nodes\AbstractObjectNode;
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

			if ($type === null || $typeHint === stdClass::class)
			{
				// Type not resolved, deserialize as anonymous object
				$objectNode = AnonymousObjectNode
					::fromSerializedInstance($serializedValue)
					->withParent($parent)
				;

				return $this->visitObject($objectNode)->getInstance();
			}

			$objectReflector = new ReflectionClass($type);

			$object = ObjectHelper::getInstance($type, $this->container, true);

			$objectNode = (new ObjectNode)
				->withReflector($objectReflector)
				->withMapper($this->chain->mapObject($objectReflector))
				->withInstance($object)
				->withSerializedInstance($serializedValue)
				->withParent($parent)
			;

			return $this->visitObject($objectNode)->getInstance();
		}

		$isArray = is_array($serializedValue) || ($parent instanceof MemberNode && $parent->getMapper()->isArray());

		if ($isArray)
		{
			if ($serializedValue === null)
			{
				$serializedValue = [];
			}

			$valueNode = (new ArrayNode)
				->withArray([])
				->withSerializedArray($serializedValue)
				->withParent($parent)
			;

			return $this->visitArray($valueNode, $typeHint)->getArray();
		}
		
		if ($typeHint !== null)
		{
			$object = ObjectHelper::getInstance($typeHint, $this->container, true);
			
			$node = (new ObjectNode)
				->withInstance($object)
				->withSerializedInstance($serializedValue)
				->withParent($parent)
			;
			
			foreach ($this->getObjectConverters(null, $typeHint) as $converter)
			{
				try
				{
					return $converter->deserialize($node);
				}
				catch (ResumeSerializationException $e)
				{
				}
			}
		}

		return $serializedValue;
	}

	/**
	 * @param ArrayNode $node
	 * @param string $itemTypeHint
	 *
	 * @return ArrayNode
	 *
	 * @throws SerializationException
	 */
	protected function visitArray(ArrayNode $node, $itemTypeHint = null)
	{
		foreach ($node->getSerializedArray() as $key => $value)
		{
			if ($value === null)
			{
				$node = $node->withArrayValue(null, $key);
			}
			
			$node = $node->withArrayValue($this->visit($value, null, $itemTypeHint), $key);
		}

		return $node;
	}

	/**
	 * @param AbstractObjectNode $node
	 *
	 * @return AbstractObjectNode
	 *
	 * @throws SerializationException
	 */
	protected function visitObject(AbstractObjectNode $node)
	{
		if ($this->hasContractResolver)
		{
			$contractMapper = $this->createContractObjectMapper($node);

			if ($contractMapper !== null)
			{
				$node = $node->withMapper($contractMapper);
			}
		}
		
		$mapper = $node->getMapper();

		$objectType = get_class($node->getInstance());
		
		foreach ($this->getObjectConverters($mapper->getDeserializingConverterType(), $objectType) as $converter)
		{
			try
			{
				return $node->withInstance($converter->deserialize($node));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		foreach ($mapper->mapMembers() as $memberMapperChain)
		{
			$serializedValue = $node->getSerializedMemberValue($memberMapperChain->getTop(false)->getSerializedName());
			
			$memberNode = (new MemberNode)
				->withSerializedValue($serializedValue)
				->withReflector($memberMapperChain->getTarget())
				->withMapper($memberMapperChain->getTop(false))
				->withParent($node)
			;

			$node->setInstanceMember($this->visitObjectMember($memberNode));
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
		if ($this->hasContractResolver)
		{
			$contractMapper = $this->createContractMemberMapper($node);
			
			if ($contractMapper !== null)
			{
				$node = $node->withMapper($contractMapper);

				// Refresh serialized value with possibly different name from contract mapper
				$node = $node->withSerializedValue($node->getParent()->getSerializedMemberValue($node->getMapper()->getSerializedName()));
			}
		}
		
		$mapper = $node->getMapper();
		
		if (!$mapper->isIncluded() || !$mapper->isDeserializable())
		{
			return $node;
		}

		$memberType = $this->getType($node->getSerializedValue(), $node);
		
		foreach ($this->getMemberConverters($mapper->getDeserializingConverterType(), $memberType) as $converter)
		{
			try
			{
				return $node->withValue($converter->deserialize($node, $memberType));
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
	 * @param AbstractObjectNode $node
	 *
	 * @return ObjectMapperInterface
	 */
	protected function createContractObjectMapper(AbstractObjectNode $node)
	{
		$mapper = $this->configuration->contractResolver->createDeserializingObjectContract($node);
		
		if ($mapper !== null)
		{
			$mapper->setChain($node->getMapper()->getChain());

			if ($node->getMapper()->getTarget() !== null)
			{
				$mapper->setTarget($node->getMapper()->getTarget());
			}
		}
		
		return $mapper;
	}

	/**
	 * @param MemberNode $node
	 *
	 * @return MemberMapperInterface
	 */
	protected function createContractMemberMapper(MemberNode $node)
	{
		$mapper = $this->configuration->contractResolver->createDeserializingMemberContract($node);

		if ($mapper !== null)
		{
			$mapper->setChain($node->getMapper()->getChain());

			if ($node->getMapper()->getTarget() !== null)
			{
				$mapper->setTarget($node->getMapper()->getTarget());
			}
		}
		
		return $mapper;
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
	 * @param mixed $serializedValue
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
			$metaHint = Metadata::get($serializedValue, Metadata::TYPE);

			// Check meta hints with the whitelist
			if ($this->configuration->getMetaHintWhitelist()->isWhitelisted($metaHint))
			{
				$typeHint = $metaHint;
			}
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
