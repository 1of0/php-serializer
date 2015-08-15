<?php


namespace OneOfZero\Json\Test\FixtureClasses;


class SimpleObject implements EqualityInterface
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

	/**
	 * @param EqualityInterface $object
	 * @return bool
	 */
	public function __equals($object)
	{
		/** @var SimpleObject $object */
		return !is_null($object)
			&& get_class($object) === self::class
			&& $object->foo == $this->foo
			&& $object->bar == $this->bar
		;
	}
}