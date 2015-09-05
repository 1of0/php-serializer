<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;


class DeserializationState
{
	/**
	 * @var array $serializedState
	 */
	public $serializedState;

	/**
	 * @var object $parentObject
	 */
	public $deserializedState;

	/**
	 * @param array $serializedState
	 * @param object $deserializedState
	 */
	public function __construct(array $serializedState, $deserializedState)
	{
		$this->serializedState = $serializedState;
		$this->deserializedState = $deserializedState;
	}
}