<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

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