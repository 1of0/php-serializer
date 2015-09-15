<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

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
		return ['abc' => $instance->foo];
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
