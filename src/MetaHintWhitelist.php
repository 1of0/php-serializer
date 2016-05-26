<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

/**
 * The deserialization type can be read from the input JSON. This is however quite dangerous and has the potential of 
 * facilitating remote code execution. To address that issue the MetaHintWhitelist specifies to which types the engine 
 * may deserialize based on class name, namespace or inheritance criteria.
 */
class MetaHintWhitelist
{
	/**
	 * @var string[] $classes
	 */
	protected $classes = [];

	/**
	 * @var string[] $interfaces
	 */
	protected $interfaces = [];

	/**
	 * @var string[] $namespaces
	 */
	protected $namespaces = [];

	/**
	 * @var string[] $patterns
	 */
	protected $patterns = [];

	/**
	 * Enables meta type hints where the hinted class is the provided class.
	 *
	 * @param string $class
	 * 
	 * @return self
	 */
	public function allowClass($class)
	{
		$this->classes[] = ltrim($class, '\\');
		return $this;
	}

	/**
	 * Enables meta type hints where the hinted class implements the provided interface.
	 *
	 * @param string $interface
	 *
	 * @return self
	 */
	public function allowClassesImplementing($interface)
	{
		$this->interfaces[] = $interface;
		return $this;
	}

	/**
	 * Enables meta type hints where the hinted class is in the provided namespace (or any of its sub-namespaces).
	 *
	 * @param string $namespace
	 *
	 * @return self
	 */
	public function allowClassesInNamespace($namespace)
	{
		$this->namespaces[] = trim($namespace, '\\') . '\\';
		return $this;
	}

	/**
	 * Enables meta type hints where the hinted class matches the provided regular expression.
	 *
	 * @param string $pattern
	 *
	 * @return self
	 */
	public function allowClassesMatchingPattern($pattern)
	{
		$this->patterns[] = $pattern;
		return $this;
	}

	/**
	 * Returns whether or not the provided class is whitelisted according to the configured rules.
	 *
	 * @param string $class
	 *
	 * @return bool
	 */
	public function isWhitelisted($class)
	{
		$class = ltrim($class, '\\');

		if (!class_exists($class))
		{
			return false;
		}

		if (in_array($class, $this->classes, true))
		{
			return true;
		}

		foreach (class_parents($class) as $parent)
		{
			if (in_array($parent, $this->classes, true))
			{
				return true;
			}
		}

		foreach ($this->interfaces as $interface)
		{
			if (in_array($interface, class_implements($class)))
			{
				return true;
			}
		}

		foreach ($this->namespaces as $namespace)
		{
			if (strlen($namespace) < strlen($class) && substr($class, 0, strlen($namespace)) === $namespace)
			{
				return true;
			}
		}

		foreach ($this->patterns as $pattern)
		{
			if (preg_match($pattern, $class))
			{
				return true;
			}
		}

		return false;
	}
}
