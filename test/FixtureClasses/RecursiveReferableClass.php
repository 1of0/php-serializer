<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

class RecursiveReferableClass extends ReferableClass
{
	/**
	 * @var RecursiveReferableClass $foo
	 */
	public $foo;
}
