<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\Annotations\JsonProperty;

class ClassReferencingReferableClass
{
	public $foo;

	public $bar;

	/**
	 * @JsonProperty(isReference=true)
	 * @var ReferableClass $reference
	 */
	public $reference;
}