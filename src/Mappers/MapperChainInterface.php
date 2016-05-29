<?php

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Configuration;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

interface MapperChainInterface
{
	/**
	 * @return Configuration
	 */
	public function getConfiguration();

	/**
	 * @return Reflector|ReflectionClass|ReflectionMethod|ReflectionProperty
	 */
	public function getTarget();
	
	/**
	 * @return MapperInterface|ObjectMapperInterface|MemberMapperInterface
	 */
	public function getTop();
	
	/**
	 * @param MapperInterface|ObjectMapperInterface|MemberMapperInterface $caller
	 *
	 * @return MapperInterface|ObjectMapperInterface|MemberMapperInterface
	 */
	public function getNext(MapperInterface $caller);
}
