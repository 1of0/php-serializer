<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Abstract implementation of a mapper that maps the serialization metadata for a class.
 */
abstract class AbstractObjectMapper implements ObjectMapperInterface
{
	/**
	 * Holds the target class.
	 * 
	 * @var ReflectionClass $target
	 */
	protected $target;

	/**
	 * Holds cached field mappers for the class properties.
	 * 
	 * @var AbstractMemberMapper[]|null $properties
	 */
	protected $properties = null;

	/**
	 * Holds cached field mappers for the class methods.
	 * 
	 * @var AbstractMemberMapper[]|null $methods
	 */
	protected $methods = null;

	/**
	 * {@inheritdoc}
	 * @return ReflectionClass
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * {@inheritdoc}
	 * @param ReflectionClass $target
	 */
	public final function setTarget($target)
	{
		$this->target = $target;
	}

	/**
	 * Should return an instance of an uninitialized field mapper.
	 * 
	 * @return AbstractMemberMapper
	 */
	protected abstract function getMemberMapper();

	/**
	 * {@inheritdoc}
	 */
	public function wantsExplicitInclusion()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function wantsNoMetadata()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		return null;
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
		$property = $this->target->getProperty($name);
		
		if ($property !== null)
		{
			return $this->mapMember($property);
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
		$method = $this->target->getMethod($name);
		
		if ($method !== null)
		{
			return $this->mapMember($method);
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
	 * @return AbstractMemberMapper[]
	 */
	private function mapMembers($fields, $filterMagic = false)
	{
		$fieldMappings = [];

		foreach ($fields as $field)
		{
			// Skip magic properties/methods
			if ($filterMagic && strpos($field->name, '__') === 0)
			{
				continue;
			}
			
			$fieldMappings[] = $this->mapMember($field);
		}

		return $fieldMappings;
	}

	/**
	 * Creates, provisions, and returns a field mapper for the provided reflection object.
	 * 
	 * @param ReflectionProperty|ReflectionMethod $field
	 * 
	 * @return AbstractMemberMapper
	 */
	private function mapMember($field)
	{
		$fieldMapping = $this->getMemberMapper();
		$fieldMapping->setParent($this);
		$fieldMapping->setTarget($field);
		return $fieldMapping;
	}
}
