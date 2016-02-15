<?php

namespace OneOfZero\Json\Internals;

use OneOfZero\Json\Internals\Mappers\MemberMapperInterface;
use ReflectionMethod;
use ReflectionProperty;

class MemberContext
{
	/**
	 * @var mixed $value
	 */
	public $value;

	/**
	 * @var mixed $serializedValue
	 */
	public $serializedValue;

	/**
	 * @var ReflectionMethod|ReflectionProperty $reflector
	 */
	private $reflector;

	/**
	 * @var MemberMapperInterface $mapper
	 */
	private $mapper;

	/**
	 * @var ObjectContext $parentContext
	 */
	private $parentContext;

	/**
	 * @param mixed $value
	 * @param mixed $serializedValue
	 * @param ReflectionMethod|ReflectionProperty $reflector
	 * @param MemberMapperInterface $mapper
	 * @param ObjectContext $parentContext
	 */
	public function __construct(
		$value,
		$serializedValue,
		$reflector,
		MemberMapperInterface $mapper,
		ObjectContext $parentContext
	)
	{
		$this->value = $value;
		$this->serializedValue = $serializedValue;
		$this->reflector = $reflector;
		$this->mapper = $mapper;
		$this->parentContext = $parentContext;
	}

	/**
	 * @return ReflectionMethod|ReflectionProperty
	 */
	public function getReflector()
	{
		return $this->reflector;
	}

	/**
	 * @return MemberMapperInterface
	 */
	public function getMapper()
	{
		return $this->mapper;
	}

	/**
	 * @return ObjectContext
	 */
	public function getParentContext()
	{
		return $this->parentContext;
	}
}