<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

use OneOfZero\Json\Internals\DeserializationState;
use OneOfZero\Json\Internals\SerializationState;

interface CustomMemberConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function canConvert($class);

	/**
	 * @param mixed $object
	 * @param string $memberName
	 * @param string $memberClass
	 * @param SerializationState $parent
	 * @return string
	 */
	public function serialize($object, $memberName, $memberClass, SerializationState $parent);

	/**
	 * @param mixed $data
	 * @param string $memberName
	 * @param string $memberClass
	 * @param DeserializationState $parent
	 * @return mixed
	 */
	public function deserialize($data, $memberName, $memberClass, DeserializationState $parent);
}
