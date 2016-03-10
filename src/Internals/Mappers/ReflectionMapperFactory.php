<?php

namespace OneOfZero\Json\Internals\Mappers;

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
