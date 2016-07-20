<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Nodes;

use OneOfZero\Json\Helpers\ReflectionHelper;
use OneOfZero\Json\Mappers\MemberMapperInterface;
use ReflectionMethod;
use ReflectionProperty;
use stdClass;

class MemberNode extends AbstractNode
{
	/**
	 * @var mixed $value
	 */
	private $value;

	/**
	 * @var mixed $serializedValue
	 */
	private $serializedValue;

	/**
	 * @var ReflectionMethod|ReflectionProperty $reflector
	 */
	private $reflector;

	/**
	 * @var MemberMapperInterface $mapper
	 */
	private $mapper;

	/**
	 * @return ObjectNode
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @param AbstractObjectNode $objectNode
	 * 
	 * @return mixed
	 */
	public function getObjectValue(AbstractObjectNode $objectNode)
	{
		if ($objectNode->getInstance() instanceof stdClass)
		{
			return $objectNode->getInstance()->{$this->mapper->getDeserializedName()};
		}
		
		$this->reflector->setAccessible(true);

		if ($this->reflector instanceof ReflectionProperty)
		{
			return $this->reflector->getValue($objectNode->getInstance());
		}

		if (ReflectionHelper::hasGetterSignature($this->reflector))
		{
			return $this->reflector->invoke($objectNode->getInstance());
		}

		return null;
	}

	/**
	 * @param AbstractObjectNode $objectNode
	 * 
	 * @param mixed $value
	 */
	public function setObjectValue(AbstractObjectNode $objectNode, $value)
	{
		if ($objectNode->getInstance() instanceof stdClass)
		{
			$objectNode->getInstance()->{$this->mapper->getDeserializedName()} = $value;
			return;
		}
		
		$this->reflector->setAccessible(true);

		if ($this->reflector instanceof ReflectionProperty)
		{
			$this->reflector->setValue($objectNode->getInstance(), $value);
			return;
		}

		if (ReflectionHelper::hasSetterSignature($this->reflector))
		{
			$this->reflector->invoke($objectNode->getInstance(), $value);
			return;
		}
	}
	
	#region // Generic immutability helpers

	/**
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function withValue($value)
	{
		$new = clone $this;
		$new->value = $value;
		return $new;
	}

	/**
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function withSerializedValue($value)
	{
		$new = clone $this;
		$new->serializedValue = $value;
		return $new;
	}

	/**
	 * @param ReflectionProperty|ReflectionMethod $reflector
	 *
	 * @return self
	 */
	public function withReflector($reflector)
	{
		$new = clone $this;
		$new->reflector = $reflector;
		return $new;
	}

	/**
	 * @param MemberMapperInterface $mapper
	 *
	 * @return self
	 */
	public function withMapper(MemberMapperInterface $mapper)
	{
		$new = clone $this;
		$new->mapper = $mapper;
		return $new;
	}

	#endregion

	#region // Generic getters and setters

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	/**
	 * @return mixed
	 */
	public function getSerializedValue()
	{
		return $this->serializedValue;
	}

	/**
	 * @return ReflectionMethod|ReflectionProperty
	 */
	public function getReflector()
	{
		return $this->reflector;
	}

	/**
	 * @return MemberMapperInterface
	 */
	public function getMapper()
	{
		return $this->mapper;
	}

	#endregion
}
