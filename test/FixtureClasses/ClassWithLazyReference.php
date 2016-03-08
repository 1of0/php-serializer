<?php

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\IsReference;

class ClassWithLazyReference
{
	/**
	 * @IsReference(lazy=true)
	 * @var ReferableClass $reference
	 */
	public $reference;
}