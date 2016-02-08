<?php

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class AbstractClassMapper
{
	/**
	 * @var ReflectionClass $target
	 */
	protected $target;

	/**
	 * @var AbstractFieldMapper[]|null $properties
	 */
	protected $properties = null;

	/**
	 * @var AbstractFieldMapper[]|null $properties
	 */
	protected $methods = null;

	/**
	 * @param ReflectionClass $target
	 */
	public final function setTarget(ReflectionClass $target)
	{
		$this->target = $target;
	}

	/**
	 * @return AbstractFieldMapper
	 */
	protected abstract function getFieldMapper();

	/**
	 * @return bool
	 */
	public function wantsExplicitInclusion()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function wantsNoMetadata()
	{
		return false;
	}

	/**
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
	 * @param string $name
	 * @return AbstractFieldMapper|null
	 */
	public final function getProperty($name)
	{
		$property = $this->target->getProperty($name);
		return $property === null ? null : $this->mapField($property);
	}

	/**
	 * @return AbstractFieldMapper[]
	 */
	public final function getMethods()
	{
		if ($this->methods === null)
		{
			$this->methods = $this->mapFields($this->target->getMethods());
		}
		return $this->methods;
	}

	/**
	 * @param string $name
	 * @return AbstractFieldMapper|null
	 */
	public final function getMethod($name)
	{
		$method = $this->target->getMethod($name);
		return $method === null ? null : $this->mapField($method);
	}

	/**
	 * @param ReflectionProperty[]|ReflectionMethod[] $fields
	 * @return AbstractFieldMapper[]
	 */
	private final function mapFields($fields)
	{
		$fieldMappings = [];

		foreach ($fields as $field)
		{
			$fieldMappings[] = $this->mapField($field);
		}

		return $fieldMappings;
	}

	/**
	 * @param ReflectionProperty|ReflectionMethod $field
	 * @return AbstractFieldMapper
	 */
	private final function mapField($field)
	{
		$fieldMapping = $this->getFieldMapper();
		$fieldMapping->setParent($this);
		$fieldMapping->setTarget($field);
		return $fieldMapping;
	}
}
