<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\Annotations\Property;

class PrivatePropertiesClass
{
	/**
	 * @Property
	 */
	private $foo;

	private $bar;

	/**
	 * PrivatePropertiesClass constructor.
	 * @param $foo
	 * @param $bar
	 */
	public function __construct($foo = null, $bar = null)
	{
		$this->foo = $foo;
		$this->bar = $bar;
	}

	/**
	 * @return mixed
	 */
	public function getFoo()
	{
		return $this->foo;
	}

	/**
	 * @return mixed
	 */
	public function getBar()
	{
		return $this->bar;
	}
}