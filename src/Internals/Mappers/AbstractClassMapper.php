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
abstract class AbstractClassMapper
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
	 * @var AbstractFieldMapper[]|null $properties
	 */
	protected $properties = null;

	/**
	 * Holds cached field mappers for the class methods.
	 * 
	 * @var AbstractFieldMapper[]|null $methods
	 */
	protected $methods = null;

	/**
	 * Sets the target context.
	 * 
	 * @param ReflectionClass $target
	 */
	public final function setTarget(ReflectionClass $target)
	{
		$this->target = $target;
	}

	/**
	 * Should return an instance of an uninitialized field mapper.
	 * 
	 * @return AbstractFieldMapper
	 */
	protected abstract function getFieldMapper();

	/**
	 * Should return a boolean value indicating whether or not fields must be explicitly included.
	 *
	 * @return bool
	 */
	public function wantsExplicitInclusion()
	{
		return false;
	}

	/**
	 * Should return a boolean value indicating whether or not the serialized representation of the class should bear
	 * library-specific metadata.
	 * 
	 * @return bool
	 */
	public function wantsNoMetadata()
	{
		return false;
	}

	/**
	 * Returns field mappers for all class properties.
	 * 
	 * @return AbstractFieldMapper[]
	 */
	public final function getProperties()
	{
		if ($this->properties === null)
		{
			$this->properties = $this->mapFields($this->target->getProperties());
		}
		return $this->properties;
	}

	/**
	 * Returns a field mapper for the property with the provided name.
	 * 
	 * @param string $name
	 * 
	 * @return AbstractFieldMapper|null
	 */
	public final function getProperty($name)
	{
		$property = $this->target->getProperty($name);
		
		if ($property !== null)
		{
			return $this->mapField($property);
		}
		
		return null;
	}

	/**
	 * Returns field mappers for all class methods.
	 * 
	 * @return AbstractFieldMapper[]
	 */
	public final function getMethods()
	{
		if ($this->methods === null)
		{
			$this->methods = $this->mapFields($this->target->getMethods(), true);
		}
		return $this->methods;
	}

	/**
	 * Returns a field mapper for the method with the provided name.
	 * 
	 * @param string $name
	 * 
	 * @return AbstractFieldMapper|null
	 */
	public final function getMethod($name)
	{
		$method = $this->target->getMethod($name);
		
		if ($method !== null)
		{
			return $this->mapField($method); 
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
	 * @return AbstractFieldMapper[]
	 */
	private function mapFields($fields, $filterMagic = false)
	{
		$fieldMappings = [];

		foreach ($fields as $field)
		{
			// Skip magic properties/methods
			if ($filterMagic && strpos($field->name, '__') === 0)
			{
				continue;
			}
			
			$fieldMappings[] = $this->mapField($field);
		}

		return $fieldMappings;
	}

	/**
	 * Creates, provisions, and returns a field mapper for the provided reflection object.
	 * 
	 * @param ReflectionProperty|ReflectionMethod $field
	 * 
	 * @return AbstractFieldMapper
	 */
	private function mapField($field)
	{
		$fieldMapping = $this->getFieldMapper();
		$fieldMapping->setParent($this);
		$fieldMapping->setTarget($field);
		return $fieldMapping;
	}
}
