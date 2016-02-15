<?php

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

interface MapperInterface
{
	/**
	 * Gets the target context.
	 *
	 * @return ReflectionClass|ReflectionMethod|ReflectionProperty
	 */
	public function getTarget();

	/**
	 * Sets the target context.
	 *
	 * @param ReflectionClass|ReflectionMethod|ReflectionProperty $target
	 */
	public function setTarget($target);

	/**
	 * Should return a boolean value indicating whether or not the field has a serializing custom converter configured.
	 *
	 * @return bool
	 */
	public function hasSerializingConverter();

	/**
	 * Should return a boolean value indicating whether or not the field has a deserializing custom converter
	 * configured.
	 *
	 * @return bool
	 */
	public function hasDeserializingConverter();

	/**
	 * Should return the type of the first serializing custom converter for the field.
	 *
	 * @return string|null
	 */
	public function getSerializingConverterType();

	/**
	 * Should return the type of the first deserializing custom converter for the field.
	 *
	 * @return string|null
	 */
	public function getDeserializingConverterType();
}