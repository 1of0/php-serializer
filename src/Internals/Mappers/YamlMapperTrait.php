<?php

/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

trait YamlMapperTrait
{
	/**
	 * @var array $mapping
	 */
	protected $mapping;

	/**
	 * @param array $mapping
	 */
	public function __construct(array $mapping)
	{
		$this->mapping = $mapping;
	}
	
	/**
	 * @return array
	 */
	public function getMapping()
	{
		return $this->mapping;
	}

	/**
	 * @param string $attributeName
	 * 
	 * @return bool
	 */
	protected final function hasAttribute($attributeName)
	{
		return array_key_exists($attributeName, $this->mapping);
	}

	/**
	 * @param string $attributeName
	 * 
	 * @return mixed|null
	 */
	protected final function readAttribute($attributeName)
	{
		return array_key_exists($attributeName, $this->mapping) ? $this->mapping[$attributeName] : null;
	}

	/**
	 * @param string $alias
	 * 
	 * @return string
	 */
	protected final function resolveAlias($alias)
	{
		return $this->getFactory()->resolveAlias($alias);
	}

	/**
	 * @param string $class
	 * 
	 * @return string
	 */
	protected final function findAlias($class)
	{
		return $this->getFactory()->findAlias($class);
	}

	/**
	 * @return YamlMapperFactory
	 */
	public abstract function getFactory();
}
