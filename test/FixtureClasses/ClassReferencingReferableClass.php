<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\Annotations\IsReference;

class ClassReferencingReferableClass
{
	public $foo;

	public $bar;

	/**
	 * @IsReference
	 * @var ReferableClass $reference
	 */
	public $reference;
}