<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\CustomConverterInterface;

class PropertyDependentCustomConverter implements CustomConverterInterface
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
		if ($propertyName === 'foo')
		{
			return 1000 - $object;
		}

		if ($propertyName === 'bar')
		{
			return 1000 + $object;
		}

		return 0;
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
		if ($propertyName === 'foo')
		{
			return 1000 - $data;
		}

		if ($propertyName === 'bar')
		{
			return $data - 1000;
		}

		return 0;
	}
}