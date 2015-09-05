<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;


class SerializationState
{
	/**
	 * @var object $instance
	 */
	public $instance;

	/**
	 * @var array $serializedState
	 */
	public $serializedState;

	/**
	 * @param object $instance
	 * @param array $serializedState
	 */
	public function __construct($instance, array $serializedState)
	{
		$this->instance = $instance;
		$this->serializedState = $serializedState;
	}
}