<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Contexts;

use OneOfZero\Json\Mappers\ObjectMapperInterface;
use ReflectionClass;

class ObjectContext extends AbstractObjectContext
{
	/**
	 * @var array $metadata
	 */
	private $metadata = [];

	/**
	 * @var ReflectionClass $reflector
	 */
	private $reflector;

	/**
	 * @var ObjectMapperInterface $mapper
	 */
	private $mapper;

	/**
	 * @param MemberContext $context
	 *
	 * @return self
	 */
	public function withInstanceMember(MemberContext $context)
	{
		$new = clone $this;

		if ($context->getValue() !== null)
		{
			$context->getMapper()->setValue($new->getInstance(), $context->getValue());
		}
		
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
	public function getSerializedInstance($includeMetadata = true)
	{
		if ($includeMetadata && is_array($this->serializedInstance))
		{
			return array_merge($this->metadata, $this->serializedInstance);
		}

		return $this->serializedInstance;
	}

	/**
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function getSerializedMemberValue($name)
	{
		return array_key_exists($name, $this->serializedInstance) ? $this->serializedInstance[$name] : null;
	}

	#region // Generic immutability helpers

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
