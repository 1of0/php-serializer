<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Converters;


use DateTime;
use OneOfZero\Json\CustomConverterInterface;

class DateTimeConverter implements CustomConverterInterface
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
	 * @param mixed $objectContext
	 * @return mixed
	 */
	public function serialize($object, $propertyName, $propertyClass, $objectContext)
	{
		/** @var DateTime $object */
		return $object->getTimestamp();
	}

	/**
	 * @param string $data
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @param array $objectContext
	 * @return mixed
	 */
	public function deserialize($data, $propertyName, $propertyClass, array $objectContext)
	{
		$date = new DateTime();
		$date->setTimestamp($data);
		return $date;
	}
}