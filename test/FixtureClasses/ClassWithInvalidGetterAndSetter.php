<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\Getter;
use OneOfZero\Json\Annotations\Setter;

class ClassWithInvalidGetterAndSetter
{
	/**
	 * @var string $foo
	 */
	private $foo;

	/**
	 * @param string|null $foo
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
}
