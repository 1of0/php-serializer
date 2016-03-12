<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

class SimpleClass
{
	public $foo;

	public $bar;

	public function __construct($foo, $bar)
	{
		$this->foo = $foo;
		$this->bar = $bar;
	}
}
