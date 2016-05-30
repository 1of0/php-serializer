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

interface FactoryInterface
{
	/**
	 * @return SourceInterface
	 */
	public function getSource();
	
	/**
	 * @param ReflectionClass $target
	 * @param ObjectMapperChain $chain
	 * 
	 * @return ObjectMapperInterface
	 */
	public function getObjectMapper(ReflectionClass $target, ObjectMapperChain $chain);

	/**
	 * @param Reflector|ReflectionClass|ReflectionProperty|ReflectionMethod $target
	 * @param MemberMapperChain $chain
	 * 
	 * @return MemberMapperInterface
	 */
	public function getMemberMapper(Reflector $target, MemberMapperChain $chain);
	
	/**
	 * @param ReflectionClass $target
	 * @param ObjectMapperChain $chain
	 * 
	 * @return ObjectMapperInterface
	 */
	public function mapObject(ReflectionClass $target, ObjectMapperChain $chain);

	/**
	 * @param Reflector|ReflectionClass|ReflectionProperty|ReflectionMethod $target
	 * @param MemberMapperChain $chain
	 * 
	 * @return MemberMapperInterface
	 */
	public function mapMember(Reflector $target, MemberMapperChain $chain);
}
