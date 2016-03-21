<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

class SimpleClassExtender extends SimpleClass
{
	public $extension;

	public function __construct($foo, $bar, $baz, $extension)
	{
		parent::__construct($foo, $bar, $baz);
		$this->extension = $extension;
	}
}
