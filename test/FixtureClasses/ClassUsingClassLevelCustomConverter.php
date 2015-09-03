<?php


namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\CustomConverter;

/**
 * @CustomConverter(CustomObjectConverter::class)
 */
class ClassUsingClassLevelCustomConverter
{
	public $foo;
}