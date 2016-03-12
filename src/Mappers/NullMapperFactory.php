<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use ReflectionClass;

class NullMapperFactory implements MapperFactoryInterface
{
	use BaseFactoryTrait;
	
	/**
	 * {@inheritdoc}
	 */
	public function mapObject(ReflectionClass $reflector)
	{
		$mapper = new NullObjectMapper();

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		
		return $mapper;
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapMember($reflector, ObjectMapperInterface $memberParent)
	{
		$mapper = new NullMemberMapper();

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		
		return $mapper;
	}
}
