<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

interface MapperInterface
{
	/**
	 * Should return the mapping source.
	 * 
	 * @return SourceInterface
	 */
	public function getSource();
	
	/**
	 * Should return the reflection target of the mapper.
	 *
	 * @return Reflector|ReflectionClass|ReflectionMethod|ReflectionProperty
	 */
	public function getTarget();

	/**
	 * Should set the provided target as the reflection target of the mapper.
	 * 
	 * @param Reflector|ReflectionClass|ReflectionMethod|ReflectionProperty $reflector
	 */
	public function setTarget(Reflector $reflector);
	
	/**
	 * Should return the mapper chain.
	 *
	 * @return MapperChainInterface|ObjectMapperChain|MemberMapperChain
	 */
	public function getChain();

	/**
	 * Should set the mapper chain to the provided chain.
	 * 
	 * @param MapperChainInterface|ObjectMapperChain|MemberMapperChain $chain
	 */
	public function setChain(MapperChainInterface $chain);

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
}
