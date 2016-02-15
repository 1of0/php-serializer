<?php

namespace OneOfZero\Json\Internals;

use OneOfZero\Json\Internals\Mappers\ObjectMapperInterface;
use ReflectionClass;

class ObjectContext
{
	/**
	 * @var mixed $instance
	 */
	public $instance;

	/**
	 * @var array $serializedInstance
	 */
	public $serializedInstance;

	/**
	 * @var ReflectionClass $reflector
	 */
	private $reflector;

	/**
	 * @var ObjectMapperInterface $mapper
	 */
	private $mapper;

	/**
	 * @var MemberContext|null $parentContext
	 */
	private $parentContext;

	/**
	 * @param mixed $instance
	 * @param array $serializedInstance
	 * @param ReflectionClass $reflector
	 * @param ObjectMapperInterface $mapper
	 * @param MemberContext|null $parentContext
	 */
	public function __construct(
		$instance,
		array $serializedInstance,
		ReflectionClass $reflector,
		ObjectMapperInterface $mapper,
		MemberContext $parentContext = null
	)
	{
		$this->instance = $instance;
		$this->serializedInstance = $serializedInstance;
		$this->reflector = $reflector;
		$this->mapper = $mapper;
		$this->parentContext = $parentContext;
	}

	/**
	 * @return ReflectionClass
	 */
	public function getReflector()
	{
		return $this->reflector;
	}

	/**
	 * @return ObjectMapperInterface
	 */
	public function getMapper()
	{
		return $this->mapper;
	}

	/**
	 * @return null|MemberContext
	 */
	public function getParentContext()
	{
		return $this->parentContext;
	}
}