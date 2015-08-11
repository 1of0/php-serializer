<?php


namespace OneOfZero\Json\Converters;


use DateTime;
use OneOfZero\Json\JsonConverterInterface;

class DateTimeConverter implements JsonConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function isSupported($class)
	{
		return $class === DateTime::class || in_array(DateTime::class, class_parents($class));
	}

	/**
	 * @param mixed $object
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return string
	 */
	public function serialize($object, $propertyName, $propertyClass)
	{
		/** @var DateTime $object */
		return $object->getTimestamp();
	}

	/**
	 * @param string $json
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return mixed
	 */
	public function deserialize($json, $propertyName, $propertyClass)
	{
		$date = new DateTime();
		$date->setTimestamp($json);
		return $date;
	}
}