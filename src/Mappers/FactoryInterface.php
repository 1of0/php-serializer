<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use ReflectionClass;
use Reflector;

interface FactoryInterface
{
	/**
	 * @param ReflectionClass $target
	 * @param ObjectMapperChain $chain
	 * 
	 * @return ObjectMapperInterface
	 */
	public function mapObject(ReflectionClass $target, ObjectMapperChain $chain);

	/**
	 * @param Reflector $target
	 * @param MemberMapperChain $chain
	 * 
	 * @return MemberMapperInterface
	 */
	public function mapMember(Reflector $target, MemberMapperChain $chain);
}
