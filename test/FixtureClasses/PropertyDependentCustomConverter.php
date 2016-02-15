<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\AbstractMemberConverter;
use OneOfZero\Json\Internals\DeserializationState;
use OneOfZero\Json\Internals\SerializationState;

class PropertyDependentAbstractConverter implements AbstractMemberConverter
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function canConvert($class)
	{
		return true;
	}

	/**
	 * @param mixed $object
	 * @param string $memberName
	 * @param string $memberClass
	 * @param SerializationState $parent
	 * @return string
	 */
	public function serialize($object, $memberName, $memberClass, SerializationState $parent)
	{
		if ($memberName === 'foo')
		{
			return 1000 - $object;
		}

		if ($memberName === 'bar')
		{
			return 1000 + $object;
		}

		return 0;
	}

	/**
	 * @param mixed $data
	 * @param string $memberName
	 * @param string $memberClass
	 * @param DeserializationState $parent
	 * @return mixed
	 */
	public function deserialize($data, $memberName, $memberClass, DeserializationState $parent)
	{
		if ($memberName === 'foo')
		{
			return 1000 - $data;
		}

		if ($memberName === 'bar')
		{
			return $data - 1000;
		}

		return 0;
	}
}
