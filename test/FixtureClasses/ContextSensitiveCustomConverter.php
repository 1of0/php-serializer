<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\CustomConverterInterface;

class ContextSensitiveCustomConverter implements CustomConverterInterface
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
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @param mixed $objectContext
	 * @return string
	 */
	public function serialize($object, $propertyName, $propertyClass, $objectContext)
	{
		/** @var ClassUsingCustomConverters $objectContext */
		return intval($object) * intval($objectContext->referableClass->getId());
	}

	/**
	 * @param mixed $data
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @param array $objectContext
	 * @return mixed
	 */
	public function deserialize($data, $propertyName, $propertyClass, array $objectContext)
	{
		return intval($data) / intval($objectContext['referableClass']);
	}
}