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
	 * @var DateTime $dateObject
	 */
	public $dateObject;

	/**
	 * @Type(SimpleClass::class)
	 * @CustomConverter(ClassDependentCustomConverter::class)
	 * @var SimpleClass $simpleClass
	 */
	public $simpleClass;

	/**
	 * @Type(ReferableClass::class)
	 * @CustomConverter(ClassDependentCustomConverter::class)
	 * @var ReferableClass $referableClass
	 */
	public $referableClass;

	/**
	 * @CustomConverter(PropertyDependentCustomConverter::class)
	 * @var int $foo
	 */
	public $foo;

	/**
	 * @CustomConverter(PropertyDependentCustomConverter::class)
	 * @var int $bar
	 */
	public $bar;

	/**
	 * @CustomConverter(ContextSensitiveCustomConverter::class)
	 * @var int $contextSensitive
	 */
	public $contextSensitive;
}