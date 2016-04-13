<?php

namespace OneOfZero\Json;

class MetaHintWhitelist
{
	protected $classes = [];

	protected $interfaces = [];

	protected $namespaces = [];

	protected $patterns = [];

	/**
	 * Enables meta type hints where the hinted class is the provided class.
	 *
	 * @param string $class
	 */
	public function allowClass($class)
	{
		$this->classes[] = ltrim($class, '\\');
	}

	/**
	 * Enables meta type hints where the hinted class implements the provided interface.
	 *
	 * @param string $interface
	 */
	public function allowClassesImplementing($interface)
	{
		$this->interfaces[] = $interface;
	}

	/**
	 * Enables meta type hints where the hinted class is in the provided namespace (or any of its sub-namespaces).
	 *
	 * @param string $namespace
	 */
	public function allowClassesInNamespace($namespace)
	{
		$this->namespaces[] = trim($namespace, '\\') . '\\';
	}

	/**
	 * Enables meta type hints where the hinted class matches the provided regular expression.
	 *
	 * @param string $pattern
	 */
	public function allowClassesMatchingPattern($pattern)
	{
		$this->patterns[] = $pattern;
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