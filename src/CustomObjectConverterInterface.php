<?php


namespace OneOfZero\Json;


interface CustomObjectConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function canConvert($class);

	/**
	 * @param object $instance
	 * @param string $class
	 * @return mixed
	 */
	public function serialize($instance, $class);

	/**
	 * @param mixed $serializedState
	 * @param string $class
	 * @return object
	 */
	public function deserialize($serializedState, $class);
}