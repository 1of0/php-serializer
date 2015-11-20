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
	 * @var bool $explicitInclusion
	 */
	public $explicitInclusion = false;

	/**
	 * @var bool $noMetadata
	 */
	public $noMetadata = false;

	/**
	 * @param ReflectionClass $target
	 */
	public function setTarget(ReflectionClass $target)
	{
		$this->target = $target;
	}

	/**
	 *
	 */
	public abstract function map();

	/**
	 * @return AbstractFieldMapper
	 */
	protected abstract function getFieldMapper();

	/**
	 * @return AbstractFieldMapper[]
	 */
	public function getProperties()
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
	public function getProperty($name)
	{
		$property = $this->target->getProperty($name);
		return $property === null ? null : $this->mapField($property);
	}

	/**
	 * @return AbstractFieldMapper[]
	 */
	public function getMethods()
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
	public function getMethod($name)
	{
		$method = $this->target->getMethod($name);
		return $method === null ? null : $this->mapField($method);
	}

	/**
	 * @param ReflectionProperty[]|ReflectionMethod[] $fields
	 * @return AbstractFieldMapper[]
	 */
	protected function mapFields($fields)
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
	protected function mapField($field)
	{
		$fieldMapping = $this->getFieldMapper();
		$fieldMapping->setTarget($field);
		$fieldMapping->map();
		return $fieldMapping;
	}
}
