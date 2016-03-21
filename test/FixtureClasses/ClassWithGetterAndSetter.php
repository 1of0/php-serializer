<?php

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\Getter;
use OneOfZero\Json\Annotations\Setter;

class ClassWithGetterAndSetter
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
	 * @Getter
	 * @return string
	 */
	public function getFoo()
	{
		return $this->foo;
	}

	/**
	 * @Setter
	 * @param string $foo
	 */
	public function setFoo($foo)
	{
		$this->foo = $foo;
	}
}