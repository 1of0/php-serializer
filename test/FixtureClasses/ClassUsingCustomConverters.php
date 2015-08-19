<?php


namespace OneOfZero\Json\Test\FixtureClasses;

use DateTime;
use OneOfZero\Json\Annotations\Type;
use OneOfZero\Json\Converters\DateTimeConverter;
use OneOfZero\Json\Annotations\CustomConverter;

class ClassUsingCustomConverters
{
	/**
	 * @Type(DateTime::class)
	 * @CustomConverter(DateTimeConverter::class)
	 */
	public $dateObject;

	/**
	 * @Type(SimpleClass::class)
	 * @CustomConverter(ClassDependentCustomConverter::class)
	 */
	public $simpleClass;

	/**
	 * @Type(ReferableClass::class)
	 * @CustomConverter(ClassDependentCustomConverter::class)
	 */
	public $referableClass;

	/**
	 * @CustomConverter(PropertyDependentCustomConverter::class)
	 */
	public $foo;

	/**
	 * @CustomConverter(PropertyDependentCustomConverter::class)
	 */
	public $bar;
}