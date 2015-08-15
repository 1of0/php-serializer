<?php


namespace OneOfZero\Json;


interface JsonConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function canConvert($class);

	/**
	 * @param mixed $object
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return string
	 */
	public function serialize($object, $propertyName, $propertyClass);

	/**
	 * @param mixed $data
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return mixed
	 */
	public function deserialize($data, $propertyName, $propertyClass);
}