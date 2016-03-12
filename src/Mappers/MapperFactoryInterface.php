<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Configuration;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

interface MapperFactoryInterface
{
	/**
	 * @return Configuration
	 */
	public function getConfiguration();

	/**
	 * @param Configuration $configuration
	 *
	 * @return self
	 */
	public function withConfiguration(Configuration $configuration);

	/**
	 * @return MapperFactoryInterface
	 */
	public function getParent();

	/**
	 * @param MapperFactoryInterface $parent
	 *
	 * @return self
	 */
	public function withParent(MapperFactoryInterface $parent);
	
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
