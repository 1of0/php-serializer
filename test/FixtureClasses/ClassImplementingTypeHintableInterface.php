<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

class ClassImplementingTypeHintableInterface implements TypeHintableInterface
{
	public $foo;

	public $bar;

	public $baz;

	public function __construct($foo, $bar, $baz)
	{
		$this->foo = $foo;
		$this->bar = $bar;
		$this->baz = $baz;
	}
}
