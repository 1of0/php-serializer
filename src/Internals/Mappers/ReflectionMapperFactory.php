<?php

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionClass;

class ReflectionMapperFactory implements MapperFactoryInterface
{
	use BaseFactoryTrait;

	/**
	 * @param MapperFactoryInterface|null $parent
	 */
	public function __construct(MapperFactoryInterface $parent = null)
	{
		$this->setParent($parent ?: new NullMapperFactory());
	}

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
		
		$mapper->setTarget($reflector);
		$mapper->setMemberParent($memberParent);
		$mapper->setBase($this->getParent()->mapMember($reflector, $memberParent->getBase()));
		
		return $mapper;
	}
}
