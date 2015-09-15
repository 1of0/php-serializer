<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\CustomMemberConverterInterface;
use OneOfZero\Json\Internals\DeserializationState;
use OneOfZero\Json\Internals\SerializationState;

class ClassDependentCustomConverter implements CustomMemberConverterInterface
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
	 * @param string $memberName
	 * @param string $memberClass
	 * @param SerializationState $parent
	 * @return string
	 */
	public function serialize($object, $memberName, $memberClass, SerializationState $parent)
	{
		if ($memberClass === SimpleClass::class)
		{
			/** @var SimpleClass $object */
			return implode('|', [ $object->foo, $object->bar ]);
		}

		if ($memberClass === ReferableClass::class)
		{
			/** @var ReferableClass $object */
			return $object->getId();
		}

		return null;
	}

	/**
	 * @param mixed $data
	 * @param string $memberName
	 * @param string $memberClass
	 * @param DeserializationState $parent
	 * @return mixed
	 */
	public function deserialize($data, $memberName, $memberClass, DeserializationState $parent)
	{
		if ($memberClass === SimpleClass::class)
		{
			$pieces = explode('|', $data);
			return new SimpleClass($pieces[0], $pieces[1]);
		}

		if ($memberClass === ReferableClass::class)
		{
			return new ReferableClass($data);
		}

		return null;
	}
}
