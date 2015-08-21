<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Converters;


use DateTime;
use OneOfZero\Json\JsonConverterInterface;

class DateTimeConverter implements JsonConverterInterface
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
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return mixed
	 */
	public function serialize($object, $propertyName, $propertyClass)
	{
		/** @var DateTime $object */
		return $object->getTimestamp();
	}

	/**
	 * @param string $data
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return mixed
	 */
	public function deserialize($data, $propertyName, $propertyClass)
	{
		$date = new DateTime();
		$date->setTimestamp($data);
		return $date;
	}
}