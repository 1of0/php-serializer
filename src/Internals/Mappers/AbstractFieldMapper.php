<?php

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionMethod;
use ReflectionProperty;

abstract class AbstractFieldMapper
{
	/**
	 * @var ReflectionProperty|ReflectionMethod $target
	 */
	protected $target;

	/**
	 * @var bool $hasIgnore
	 */
	public $hasIgnore = false;

	/**
	 * @var string $name
	 */
	public $name;

	/**
	 * @var bool $isArray
	 */
	public $isArray = false;

	/**
	 * @var bool $isReference
	 */
	public $isReference = false;

	/**
	 * @var bool $lazyResolution
	 */
	public $isReferenceLazy;

	/**
	 * @var bool $isGetter
	 */
	public $isGetter = false;

	/**
	 * @var bool $isSetter
	 */
	public $isSetter = false;

	/**
	 * @var bool $isProperty
	 */
	public $isProperty = false;

	/**
	 * @var bool $serialize
	 */
	public $serialize = true;

	/**
	 * @var bool $deserialize
	 */
	public $deserialize = true;

	/**
	 * @var bool $hasCustomConverter
	 */
	public $hasCustomConverter = false;

	/**
	 * @var string $customConverterClass
	 */
	public $customConverterClass;

	/**
	 * @var bool $customConverterSerializes
	 */
	public $customConverterSerializes;

	/**
	 * @var bool $customConverterDeserializes
	 */
	public $customConverterDeserializes;

	/**
	 * @var bool $hasType
	 */
	public $hasType;

	/**
	 * @var string $type
	 */
	public $type;

	/**
	 * @param ReflectionProperty|ReflectionMethod $target
	 */
	public function setTarget($target)
	{
		$this->target = $target;
	}

	/**
	 *
	 */
	public abstract function map();

	/**
	 * @return bool
	 */
	public function isClassProperty()
	{
		return $this->target instanceof ReflectionProperty;
	}

	/**
	 * @return bool
	 */
	public function isClassMethod()
	{
		return $this->target instanceof ReflectionMethod;
	}
}
