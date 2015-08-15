<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\Converters\DateTimeConverter;
use OneOfZero\Json\Annotations\JsonConverter;

class ClassUsingCustomConverters
{
	/**
	 * @JsonConverter(DateTimeConverter::class)
	 */
	public $dateObject;

	/**
	 * @JsonConverter(ClassDependentCustomConverter::class)
	 */
	public $variableObject;

	/**
	 * @JsonConverter(PropertyDependentCustomConverter::class)
	 */
	public $foo;

	/**
	 * @JsonConverter(PropertyDependentCustomConverter::class)
	 */
	public $bar;
}