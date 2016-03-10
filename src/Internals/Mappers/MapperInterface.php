<?php

namespace OneOfZero\Json\Internals\Mappers;

use OneOfZero\Json\Configuration;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

interface MapperInterface
{
	/**
	 * @return Configuration
	 */
	public function getConfiguration();

	/**
	 * Should return the base mapper.
	 *
	 * @return self
	 */
	public function getBase();

	/**
	 * Should set the provided mapper as base mapper.
	 *
	 * @param MapperInterface $mapper
	 */
	public function setBase(MapperInterface $mapper);

	/**
	 * Should return the mapper factory.
	 *
	 * @return MapperFactoryInterface
	 */
	public function getFactory();

	/**
	 * Should store the provided mapper factory.
	 *
	 * @param MapperFactoryInterface $factory
	 */
	public function setFactory(MapperFactoryInterface $factory);

	/**
	 * Should return the reflection target of the member.
	 *
	 * @return ReflectionClass|ReflectionMethod|ReflectionProperty
	 */
	public function getTarget();

	/**
	 * Should set the provided target as reflection target for the member.
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
