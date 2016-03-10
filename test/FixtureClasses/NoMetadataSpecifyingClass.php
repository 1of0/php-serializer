<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\NoMetadata;

/**
 * @NoMetadata
 */
class NoMetadataSpecifyingClass
{
	public $foo;

	public $bar;

	/**
	 * @param $foo
	 * @param $bar
	 */
	public function __construct($foo, $bar)
	{
		$this->foo = $foo;
		$this->bar = $bar;
	}
}
