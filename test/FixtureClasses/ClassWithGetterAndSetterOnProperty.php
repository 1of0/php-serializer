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

class ClassWithGetterAndSetterOnProperty
{
	/**
	 * @Getter
	 * @var string $foo
	 */
	private $foo;

	/**
	 * @Setter
	 * @var string $bar
	 */
	private $bar;

	/**
	 * @param string|null $foo
	 * @param string|null $bar
	 */
	public function __construct($foo = null, $bar = null)
	{
		$this->foo = $foo;
		$this->bar = $bar;
	}
}
