<?php

namespace OneOfZero\Json\Mappers\AbstractArray;

use ReflectionClass;
use OneOfZero\Json\Mappers\SourceInterface;
use ReflectionMethod;
use ReflectionProperty;

abstract class ArrayAbstractSource implements SourceInterface
{
	/**
	 * @var array $aliases
	 */
	protected $aliases = [];

	/**
	 * @var array $mapping
	 */
	protected $mapping = [];

	/**
	 * @var bool $loaded
	 */
	private $loaded = false;

	/**
	 * Calls when the mapping is needed, so that loading can be deferred from the constructor.
	 */
	protected abstract function load();
	
	/**
	 * @param ReflectionClass $reflector
	 * 
	 * @return array
	 */
	public function getObjectMapping(ReflectionClass $reflector)
	{
		if (!$this->loaded)
		{
			$this->load();
			$this->loaded = true;
		}
		
		$class = $reflector->name;
		
		if (array_key_exists($class, $this->mapping))
		{
			return $this->mapping[$class];
		}
		
		$alias = $this->findAlias($class);
		
		if (array_key_exists($alias, $this->mapping))
		{
			return $this->mapping[$alias];
		}
		
		return [];
	}

	/**
	 * @param ReflectionProperty|ReflectionMethod $reflector
	 * 
	 * @return array
	 */
	public function getMemberMapping($reflector)
	{
		if (!$this->loaded)
		{
			$this->load();
			$this->loaded = true;
		}
		
		$objectMapping = $this->getObjectMapping($reflector->getDeclaringClass());

		if ($reflector instanceof ReflectionProperty
			&& array_key_exists('properties', $objectMapping)
			&& array_key_exists($reflector->name, $objectMapping['properties']))
		{
			return $objectMapping['properties'][$reflector->name];
		}

		if ($reflector instanceof ReflectionMethod
			&& array_key_exists('methods', $objectMapping)
			&& array_key_exists($reflector->name, $objectMapping['methods']))
		{
			return $objectMapping['methods'][$reflector->name];
		}
		
		return [];
	}

	/**
	 * @param string $alias
	 *
	 * @return string
	 */
	public function resolveAlias($alias)
	{
		if (!$this->loaded)
		{
			$this->load();
			$this->loaded = true;
		}
		
		return array_key_exists($alias, $this->aliases)
			? $this->aliases[$alias]
			: $alias
		;
	}

	/**
	 * @param string $class
	 *
	 * @return string
	 */
	protected function findAlias($class)
	{
		$alias = array_search($class, $this->aliases);
		return ($alias === false) ? $class : $alias;
	}
}
