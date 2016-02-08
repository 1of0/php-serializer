<?php

namespace OneOfZero\Json\Internals\Mappers;

use Closure;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

abstract class AbstractFieldMapper
{
	const GETTER_REGEX = '/^(?<prefix>get|is|has)/';
	const SETTER_REGEX = '/^(?<prefix>set)/';
	const GETTER_SETTER_REGEX = '/^(?<prefix>get|is|has|set)/';

	/**
	 * @var AbstractClassMapper $parent
	 */
	protected $parent;

	/**
	 * @var ReflectionProperty|ReflectionMethod $target
	 */
	protected $target;

	/**
	 * @var bool $hasType
	 */
	public $hasType;

	/**
	 * @var string $type
	 */
	public $type;

	/**
	 * @param AbstractClassMapper $parent
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * @param ReflectionProperty|ReflectionMethod $target
	 */
	public function setTarget($target)
	{
		$this->target = $target;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		// By default assume the target member's name
		$name = $this->target->name;

		if ($this->isClassMethod())
		{
			// For methods with a prefix, trim off prefix, and make the first character is lower case
			$name = lcfirst(substr($this->target->name, strlen($this->getMethodPrefix())));
		}

		return $name;
	}

	/**
	 * @return string|null
	 */
	public function getType()
	{
		if ($this->isGetter())
		{
			if (version_compare(PHP_VERSION, '7.0.0', '>='))
			{
				$type = $this->target->getReturnType();

				if ($type !== null && $this->isSupportedType($type))
				{
					// Determine type from PHP7 return type constraint
					return $type;
				}
			}
		}

		if ($this->isSetter())
		{
			/** @var ReflectionParameter $setter */
			list($setter) = $this->target->getParameters();

			if ($setter->hasType() && $this->isSupportedType($setter->getType()))
			{
				// Determine type from first method parameter
				return $setter->getType();
			}
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function isArray()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function isGetter()
	{
		if (!$this->isClassMethod() || !preg_match(self::GETTER_REGEX, $this->target->name))
		{
			return false;
		}

		if ($this->target->getNumberOfRequiredParameters() > 0)
		{
			// Valid getters must have no required parameters
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isSetter()
	{
		if (!$this->isClassMethod() || !preg_match(self::SETTER_REGEX, $this->target->name))
		{
			return false;
		}

		if ($this->target->getNumberOfParameters() === 0 || $this->target->getNumberOfRequiredParameters() > 1)
		{
			// Valid setters must have at least one parameter, and at most one required parameter
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isReference()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function isReferenceLazy()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function hasSerializingCustomConverter()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function hasDeserializingCustomConverter()
	{
		return false;
	}

	/**
	 * @return string|null
	 */
	public function getSerializingCustomConverterType()
	{
		return null;
	}

	/**
	 * @return string|null
	 */
	public function getDeserializingCustomConverterType()
	{
		return null;
	}

	/**
	 * @return bool
	 */
	public function doesSerialization()
	{
		if ($this->isClassMethod() && !$this->isGetter())
		{
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function doesDeserialization()
	{
		if ($this->isClassMethod() && !$this->isSetter())
		{
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isIncluded()
	{
		if (!$this->target->isPublic())
		{
			// Non-public properties and methods are excluded by default
			return false;
		}

		if ($this->isClassMethod() && !$this->isGetter() && !$this->isSetter())
		{
			// Methods that are neither a valid getter or a setter are excluded by default
			return false;
		}

		if ($this->parent->wantsExplicitInclusion())
		{
			return false;
		}

		return true;
	}

	/**
	 * Returns a boolean value indicating whether or not the target field is a property.
	 *
	 * @return bool
	 */
	public function isClassProperty()
	{
		return $this->target instanceof ReflectionProperty;
	}

	/**
	 * Returns a boolean value indicating whether or not the target field is a method.
	 *
	 * @return bool
	 */
	public function isClassMethod()
	{
		return $this->target instanceof ReflectionMethod;
	}

	/**
	 * Returns a boolean value indicating whether or not the provided type is a supported type.
	 *
	 * Supported types are all object types except \Closure.
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	protected function isSupportedType($type)
	{
		return class_exists($type) && $type !== Closure::class;
	}

	/**
	 * Determine if the method name has a prefix (get/set/is/has), and return that prefix.
	 *
	 * Returns an empty string if the method name does not have a prefix.
	 *
	 * @return string
	 */
	protected function getMethodPrefix()
	{
		if ($this->isClassMethod() && preg_match(self::GETTER_SETTER_REGEX, $this->target->name, $matches))
		{
			return $matches['prefix'];
		}

		return '';
	}
}
