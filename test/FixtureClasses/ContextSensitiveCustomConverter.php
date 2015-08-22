<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\CustomConverterInterface;
use OneOfZero\Json\Internals\DeserializationState;
use OneOfZero\Json\Internals\SerializationState;

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
	 * @param SerializationState $state
	 * @return string
	 */
	public function serialize($object, $propertyName, $propertyClass, SerializationState $state)
	{
		/** @var ClassUsingCustomConverters $parentInstance */
		$parentInstance = $state->parentObject;

		return intval($object) * intval($parentInstance->referableClass->getId());
	}

	/**
	 * @param mixed $data
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @param DeserializationState $state
	 * @return mixed
	 */
	public function deserialize($data, $propertyName, $propertyClass, DeserializationState $state)
	{
		/** @var ClassUsingCustomConverters $deserializedParent */
		$deserializedParent = $state->deserializedParentState;

		return intval($data) / intval($deserializedParent->referableClass->getId());
	}
}