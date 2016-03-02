<?php

namespace OneOfZero\Json\Internals;

use OneOfZero\Json\Internals\Mappers\ObjectMapperInterface;
use ReflectionClass;
use RuntimeException;

class ObjectContext extends AbstractContext
{
	/**
	 * @var mixed $instance
	 */
	private $instance;

	/**
	 * @var array $serializedInstance
	 */
	private $serializedInstance;

	/**
	 * @var array $metadata
	 */
	private $metadata;

	/**
	 * @var ReflectionClass $reflector
	 */
	private $reflector;

	/**
	 * @var ObjectMapperInterface $mapper
	 */
	private $mapper;

	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function withSerializedMember($name, $value)
	{
		if ($this->serializedInstance !== null && !is_array($this->serializedInstance))
		{
			throw new RuntimeException('Cannot set members when the serialized instance is not an array type');
		}

		$new = clone $this;
		$new->serializedInstance[$name] = $value;
		return $new;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function withMetadata($key, $value)
	{
		$new = clone $this;
		$new->metadata[$key] = $value;
		return $new;
	}

	/**
	 * @param bool $includeMetadata
	 *
	 * @return array
	 */
	public function getSerializedInstance($includeMetadata = false)
	{
		if ($includeMetadata && is_array($this->serializedInstance))
		{
			return array_merge($this->metadata, $this->serializedInstance);
		}

		return $this->serializedInstance;
	}

	#region // Generic immutability helpers

	/**
	 * @param object $instance
	 *
	 * @return self
	 */
	public function withInstance($instance)
	{
		$new = clone $this;
		$new->instance = $instance;
		return $new;
	}

	/**
	 * @param mixed $serializedInstance
	 *
	 * @return self
	 */
	public function withSerializedInstance($serializedInstance)
	{
		$new = clone $this;
		$new->serializedInstance = $serializedInstance;
		return $new;
	}

	/**
	 * @param ReflectionClass $reflector
	 *
	 * @return self
	 */
	public function withReflector(ReflectionClass $reflector)
	{
		$new = clone $this;
		$new->reflector = $reflector;
		return $new;
	}

	/**
	 * @param ObjectMapperInterface $mapper
	 *
	 * @return self
	 */
	public function withMapper(ObjectMapperInterface $mapper)
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
	public function getInstance()
	{
		return $this->instance;
	}

	/**
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}

	/**
	 * @return ReflectionClass
	 */
	public function getReflector()
	{
		return $this->reflector;
	}

	/**
	 * @return ObjectMapperInterface
	 */
	public function getMapper()
	{
		return $this->mapper;
	}

	#endregion
}