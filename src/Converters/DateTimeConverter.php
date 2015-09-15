<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Converters;

use DateTime;
use OneOfZero\Json\CustomMemberConverterInterface;
use OneOfZero\Json\Internals\DeserializationState;
use OneOfZero\Json\Internals\SerializationState;

class DateTimeConverter implements CustomMemberConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function canConvert($class)
	{
		return $class !== null && ($class === DateTime::class || in_array(DateTime::class, class_parents($class)));
	}

	/**
	 * @param mixed $object
	 * @param string $memberName
	 * @param string $memberClass
	 * @param SerializationState $parent
	 * @return mixed
	 */
	public function serialize($object, $memberName, $memberClass, SerializationState $parent)
	{
		/** @var DateTime $object */
		return $object->getTimestamp();
	}

	/**
	 * @param string $data
	 * @param string $memberName
	 * @param string $memberClass
	 * @param DeserializationState $parent
	 * @return mixed
	 */
	public function deserialize($data, $memberName, $memberClass, DeserializationState $parent)
	{
		$date = new DateTime();
		$date->setTimestamp($data);
		return $date;
	}
}
