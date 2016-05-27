<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Caching;

use OneOfZero\Json\Mappers\BaseMapperTrait;
use OneOfZero\Json\Mappers\ObjectMapperInterface;
use ReflectionMethod;
use ReflectionProperty;

class CachedObjectMapper implements ObjectMapperInterface
{
	use BaseMapperTrait;

	/**
	 * @var array $mapping
	 */
	private $mapping;

	/**
	 * @param array $mapping
	 */
	public function __construct(array $mapping)
	{
		$this->mapping = $mapping;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isExplicitInclusionEnabled()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isMetadataDisabled()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMembers()
	{
		return array_merge($this->getProperties(), $this->getMethods());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getProperties()
	{
		$properties = [];
		
		foreach ($this->mapping['__properties'] as $name => $mapping)
		{
			$mapper = new CachedMemberMapper($mapping);
			$mapper->setTarget(new ReflectionProperty($this->target->name, $name));
			$mapper->setFactory($this->getFactory());
			
			$properties[] = $mapper;
		}
		
		return $properties;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMethods()
	{
		$methods = [];

		foreach ($this->mapping['__methods'] as $name => $mapping)
		{
			$mapper = new CachedMemberMapper($mapping);
			$mapper->setTarget(new ReflectionMethod($this->target->name, $name));
			$mapper->setFactory($this->getFactory());

			$methods[] = $mapper;
		}

		return $methods;
	}
}
