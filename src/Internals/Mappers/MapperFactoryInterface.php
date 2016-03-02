<?php

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

interface MapperFactoryInterface
{
	/**
	 * @return MapperFactoryInterface
	 */
	public function getParent();

	/**
	 * @param MapperFactoryInterface $parent
	 */
	public function setParent(MapperFactoryInterface $parent);
	
	/**
	 * @param ReflectionClass $reflector
	 *
	 * @return ObjectMapperInterface
	 */
	public function mapObject(ReflectionClass $reflector);

	/**
	 * @param ReflectionProperty|ReflectionMethod $reflector
	 * @param ObjectMapperInterface $memberParent
	 * 
	 * @return MemberMapperInterface
	 */
	public function mapMember($reflector, ObjectMapperInterface $memberParent);
}
