<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Reflection;

use OneOfZero\Json\Mappers\BaseFactoryTrait;
use OneOfZero\Json\Mappers\MapperFactoryInterface;
use OneOfZero\Json\Mappers\ObjectMapperInterface;
use ReflectionClass;

class ReflectionMapperFactory implements MapperFactoryInterface
{
	use BaseFactoryTrait;

	/**
	 * {@inheritdoc}
	 */
	public function mapObject(ReflectionClass $reflector)
	{
		$mapper = new ReflectionObjectMapper();

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		$mapper->setBase($this->getParent()->mapObject($reflector));
		
		return $mapper;
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapMember($reflector, ObjectMapperInterface $memberParent)
	{
		$mapper = new ReflectionMemberMapper();

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		$mapper->setMemberParent($memberParent);
		$mapper->setBase($this->getParent()->mapMember($reflector, $memberParent->getBase()));
		
		return $mapper;
	}
}
