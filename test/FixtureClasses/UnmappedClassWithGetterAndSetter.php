<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

class UnmappedClassWithGetterAndSetter
{
	/**
	 * @var string $foo
	 */
	private $foo;

	/**
	 * @param string $foo
	 */
	public function __construct($foo = null)
	{
		$this->foo = $foo;
	}
	
	/**
	 * @return string
	 */
	public function getFoo()
	{
		return $this->foo;
	}

	/**
	 * @param string $foo
	 */
	public function setFoo($foo)
	{
		$this->foo = $foo;
	}
}