<?php

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\Getter;
use OneOfZero\Json\Annotations\Setter;

class LongPropertyNamesClass
{
	/**
	 * @var string $firstPropertyName
	 */
	public $firstPropertyName;

	/**
	 * @var string $secondPropertyName
	 */
	public $secondPropertyName;

	/**
	 * @var string $methodSetProperty
	 */
	private $methodSetProperty;

	/**
	 * @Getter
	 * @return string
	 */
	public function getAnExampleMethodName()
	{
		return $this->methodSetProperty;
	}

	/**
	 * @Setter
	 * @param string $value
	 */
	public function setAnExampleMethodName($value)
	{
		$this->methodSetProperty = $value;
	}
}