<?php


namespace OneOfZero\Json\Test\FixtureClasses;


class SimpleClass
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