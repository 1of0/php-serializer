<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\Converter;

/**
 * @Converter(SimpleClass::class)
 */
class ClassUsingInvalidTypeForObjectConverter
{
	public $foo;
}
