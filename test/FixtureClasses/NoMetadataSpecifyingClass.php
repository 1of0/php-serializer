<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\Annotations\NoMetadata;

/**
 * @NoMetadata
 */
class NoMetadataSpecifyingClass
{
	public $foo;

	public $bar;

	/**
	 * @param $foo
	 * @param $bar
	 */
	public function __construct($foo = null, $bar = null)
	{
		$this->foo = $foo;
		$this->bar = $bar;
	}
}