<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\JsonConverterInterface;

class ClassDependentCustomConverter implements JsonConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function isSupported($class)
	{

	}

	/**
	 * @param mixed $object
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return string
	 */
	public function serialize($object, $propertyName, $propertyClass)
	{

	}

	/**
	 * @param string $json
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return mixed
	 */
	public function deserialize($json, $propertyName, $propertyClass)
	{

	}
}