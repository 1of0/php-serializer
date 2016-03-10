<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

class SimpleClassExtender extends SimpleClass
{
	public $baz;

	public function __construct($foo, $bar, $baz)
	{
		parent::__construct($foo, $bar);
		$this->baz = $baz;
	}
}
