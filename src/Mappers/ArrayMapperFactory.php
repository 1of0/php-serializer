<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class ArrayMapperFactory implements MapperFactoryInterface
{
	use BaseFactoryTrait;

	/**
	 * @var array $mapping
	 */
	protected $mapping = [];

	/**
	 * @var array $aliases
	 */
	protected $aliases = [];

	/**
	 * {@inheritdoc}
	 */
	public function mapObject(ReflectionClass $reflector)
	{
		$objectMapping = $this->getObjectMapping($reflector->name);

		$mapper = new ArrayObjectMapper($objectMapping);

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		$mapper->setBase($this->getParent()->mapObject($reflector));

		return $mapper;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param ArrayObjectMapper $memberParent
	 */
	public function mapMember($reflector, ObjectMapperInterface $memberParent)
	{
		$objectMapping = $memberParent->getMapping();
		$memberMapping = $this->getMemberMapping($reflector, $objectMapping);
		
		$mapper = new ArrayMemberMapper($memberMapping);

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		$mapper->setMemberParent($memberParent);
		$mapper->setBase($this->getParent()->mapMember($reflector, $memberParent->getBase()));

		return $mapper;
	}

	/**
	 * @param string $alias
	 * 
	 * @return string
	 */
	public function resolveAlias($alias)
	{
		return array_key_exists($alias, $this->aliases) ? $this->aliases[$alias] : $alias;
	}

	/**
	 * @param string $class
	 * 
	 * @return string
	 */
	public function findAlias($class)
	{
		$alias = array_search($class, $this->aliases);
		return ($alias === false) ? $class : $alias;
	}

	/**
	 * @param string $class
	 * 
	 * @return array
	 */
	private function getObjectMapping($class)
	{
		$alias = $this->findAlias($class);
		return array_key_exists($alias, $this->mapping) ? $this->mapping[$alias] : [];
	}

	/**
	 * @param ReflectionProperty|ReflectionMethod $reflector
	 * @param array $objectMapping
	 * 
	 * @return array
	 */
	private function getMemberMapping($reflector, array $objectMapping)
	{
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
}
