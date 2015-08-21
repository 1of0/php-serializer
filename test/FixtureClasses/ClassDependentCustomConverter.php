<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\CustomConverterInterface;

class ClassDependentCustomConverter implements CustomConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function canConvert($class)
	{
		return $class === SimpleClass::class || $class === ReferableClass::class;
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
		if ($propertyClass === SimpleClass::class)
		{
			/** @var SimpleClass $object */
			return implode('|', [ $object->foo, $object->bar ]);
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
	 * @param array $objectContext
	 * @return mixed
	 */
	public function deserialize($data, $propertyName, $propertyClass, array $objectContext)
	{
		if ($propertyClass === SimpleClass::class)
		{
			$pieces = explode('|', $data);
			return new SimpleClass($pieces[0], $pieces[1]);
		}

		if ($propertyClass === ReferableClass::class)
		{
			return new ReferableClass($data);
		}

		return null;
	}
}