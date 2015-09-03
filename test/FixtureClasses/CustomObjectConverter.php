<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\CustomObjectConverterInterface;

class CustomObjectConverter implements CustomObjectConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function canConvert($class)
	{
		return $class === ClassUsingClassLevelCustomConverter::class;
	}

	/**
	 * @param object $instance
	 * @param string $class
	 * @return mixed
	 */
	public function serialize($instance, $class)
	{
		return [ 'abc' => $instance->foo ];
	}

	/**
	 * @param mixed $serializedState
	 * @param string $class
	 * @return object
	 */
	public function deserialize($serializedState, $class)
	{
		$instance = new $class();
		$instance->foo = $serializedState['abc'];
		return $instance;
	}
}