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
	 * @return FactoryChain
	 */
	public function getFactoryChain();

	/**
	 * @param bool $noCache
	 * 
	 * @return MapperInterface|MemberMapperInterface|ObjectMapperInterface
	 */
	public function getTop($noCache = true);
	
	/**
	 * @param MapperInterface|ObjectMapperInterface|MemberMapperInterface $caller
	 *
	 * @return MapperInterface|ObjectMapperInterface|MemberMapperInterface
	 */
	public function getNext(MapperInterface $caller);
}
