<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionMethod;
use ReflectionProperty;

trait BaseObjectMapperTrait
{
	use BaseMapperTrait;
	
	/**
	 * Holds the parent mapper layer.
	 * 
	 * @var MapperFactoryInterface $factory
	 */
	protected $factory;
	
	/**
	 * Holds cached field mappers for the class properties.
	 *
	 * @var ReflectionMemberMapper[]|null $properties
	 */
	protected $properties = null;

	/**
	 * Holds cached field mappers for the class methods.
	 *
	 * @var ReflectionMemberMapper[]|null $methods
	 */
	protected $methods = null;
		
	/**
	 * {@inheritdoc}
	 */
	public final function getFactory()
	{
		return $this->factory;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function setFactory(MapperFactoryInterface $factory)
	{
		$this->factory = $factory;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public final function getMembers()
	{
		return array_merge($this->getProperties(), $this->getMethods());
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getProperties()
	{
		if ($this->properties === null)
		{
			$this->properties = $this->mapMembers($this->target->getProperties());
		}
		return $this->properties;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getProperty($name)
	{
		/** @var ObjectMapperInterface $this */
		
		$property = $this->getTarget()->getProperty($name);

		if ($property !== null)
		{
			return $this->getFactory()->mapMember($property, $this);
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getMethods()
	{
		if ($this->methods === null)
		{
			$this->methods = $this->mapMembers($this->target->getMethods(), true);
		}
		return $this->methods;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getMethod($name)
	{
		/** @var ObjectMapperInterface $this */
		
		$method = $this->getTarget()->getMethod($name);

		if ($method !== null)
		{
			return $this->getFactory()->mapMember($method, $this);
		}

		return null;
	}

	/**
	 * Creates, provisions, and returns field mappers for the provided reflection objects.
	 *
	 * The filterMagic parameter can be used to filter out magic methods and properties.
	 *
	 * @param ReflectionProperty[]|ReflectionMethod[] $fields
	 * @param bool $filterMagic
	 *
	 * @return MemberMapperInterface[]
	 */
	protected function mapMembers($fields, $filterMagic = false)
	{
		/** @var ObjectMapperInterface $this */
		
		$fieldMappings = [];

		foreach ($fields as $field)
		{
			// Skip magic properties/methods
			if ($filterMagic && strpos($field->name, '__') === 0)
			{
				continue;
			}

			$fieldMappings[] = $this->getFactory()->mapMember($field, $this);
		}

		return $fieldMappings;
	}
}
