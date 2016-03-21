<?php

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\Getter;
use OneOfZero\Json\Annotations\Ignore;
use OneOfZero\Json\Annotations\Setter;

class ClassWithInvalidGetterAndSetter
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
	 * @param string $nonOptionalArgument
	 * @return string
	 */
	public function getFoo($nonOptionalArgument)
	{
		return $this->foo;
	}

	/**
	 * @Setter
	 * @param string $value
	 * @param string $nonOptionalArgument
	 */
	public function setFoo($value, $nonOptionalArgument)
	{
		$this->foo = $value;
	}

	/**
	 * @Ignore
	 * @return string
	 */
	public function _getFoo()
	{
		return $this->foo;
	}

	/**
	 * @Ignore
	 * @param string $value
	 */
	public function _setFoo($value)
	{
		$this->foo = $value;
	}


}