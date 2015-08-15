<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\JsonConverterInterface;

class ClassDependentCustomConverter implements JsonConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function canConvert($class)
	{
		return $class === SimpleObject::class || $class === ReferableClass::class;
	}

	/**
	 * @param mixed $object
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return string
	 */
	public function serialize($object, $propertyName, $propertyClass)
	{
		if ($propertyClass === SimpleObject::class)
		{
			/** @var SimpleObject $object */
			return $object->foo;
		}

		if ($propertyClass === ReferableClass::class)
		{
			/** @var ReferableClass $object */
			return $object->getId();
		}

		return null;
	}

	/**
	 * @param mixed $data
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @return mixed
	 */
	public function deserialize($data, $propertyName, $propertyClass)
	{
		if ($propertyClass === SimpleObject::class)
		{
			return new SimpleObject($data);
		}

		if ($propertyClass === ReferableClass::class)
		{
			return new ReferableClass($data);
		}

		return null;
	}
}