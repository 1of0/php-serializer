<?php
/**
 * Copyright (c) 2017 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Fluent;


class Mapping
{
	/**
	 * @var MappedClass[] $classes
	 */
	private $classes = [];

	/**
	 * @param string $name
	 * @return MappedClass
	 */
	public function forClass($name)
	{
		$class = new MappedClass($this);
		$this->classes[$name] = $class;
		return $class;
	}

	/**
	 * @return array
	 */
	public function __toArray()
	{
		$structure = [];

		foreach ($this->classes as $className => $class)
		{
			$structure[$className] = $class->__toArray();
		}

		return $structure;
	}
}
