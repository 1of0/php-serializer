<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\Converter;
use OneOfZero\Json\Test\FixtureClasses\Converters\NullMemberConverter;

class ClassUsingNullMemberConverter
{
	/**
	 * @Converter(NullMemberConverter::class)
	 * @var string $foo
	 */
	public $foo;

	/**
	 * @Converter(NullMemberConverter::class)
	 * @var string $bar
	 */
	public $bar;

	public function __construct($foo, $bar)
	{
		$this->foo = $foo;
		$this->bar = $bar;
	}
}
