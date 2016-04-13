<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Exceptions\SerializationException;
use ReflectionMethod;
use ReflectionProperty;

trait BaseMemberMapperTrait
{
	use BaseMapperTrait;
	
	protected static $GETTER_REGEX = '/^(?<prefix>get|is|has)/';
	protected static $SETTER_REGEX = '/^(?<prefix>set)/';
	protected static $GETTER_SETTER_REGEX = '/^(?<prefix>get|is|has|set)/';

	/**
	 * Holds the member parent.
	 * 
	 * @var ReflectionObjectMapper $memberParent
	 */
	protected $memberParent;

	/**
	 * {@inheritdoc}
	 */
	public final function setMemberParent(ObjectMapperInterface $memberParent)
	{
		$this->memberParent = $memberParent;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializedName()
	{
		return $this->target->name;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getValue($instance)
	{
		$this->target->setAccessible(true);

		if ($this->isClassProperty())
		{
			return $this->target->getValue($instance);
		}

		if ($this->hasGetterSignature())
		{
			return $this->target->invoke($instance);
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($instance, $value)
	{
		$this->target->setAccessible(true);

		if ($this->isClassProperty())
		{
			$this->target->setValue($instance, $value);
			return;
		}

		if ($this->hasSetterSignature())
		{
			$this->target->invoke($instance, $value);
			return;
		}
	}

	/**
	 * Returns a boolean value indicating whether or not the target field is a property.
	 *
	 * @return bool
	 */
	protected final function isClassProperty()
	{
		return $this->target instanceof ReflectionProperty;
	}

	/**
	 * Returns a boolean value indicating whether or not the target field is a method.
	 *
	 * @return bool
	 */
	protected final function isClassMethod()
	{
		return $this->target instanceof ReflectionMethod;
	}

	/**
	 * @return bool
	 */
	protected final function hasGetterSignature()
	{
		// Valid getters must have no required parameters
		return $this->isClassMethod() 
		    && $this->target->getNumberOfRequiredParameters() === 0
		;
	}

	/**
	 * @return bool
	 */
	protected final function hasSetterSignature()
	{
		// Valid setters must have at least one parameter, and at most one required parameter
		return $this->isClassMethod() 
		    && $this->target->getNumberOfParameters() > 0 
		    && $this->target->getNumberOfRequiredParameters() <= 1
		;
	}

	/**
	 * @throws SerializationException
	 */
	protected final function validateGetterSignature()
	{
		if (!($this->target instanceof ReflectionMethod))
		{
			throw new SerializationException("Field {$this->target->name} is not a method. Only methods may be marked as getters.");
		}

		$paramCount = $this->target->getNumberOfRequiredParameters();

		if ($paramCount > 0)
		{
			throw new SerializationException("Field {$this->target->name} has {$paramCount} required parameters. Fields marked as getters must have no required parameters.");
		}
	}

	/**
	 * @throws SerializationException
	 */
	protected final function validateSetterSignature()
	{
		if (!($this->target instanceof ReflectionMethod))
		{
			throw new SerializationException("Field {$this->target->name} is not a method. Only methods may be marked as setters.");
		}
		
		if ($this->target->getNumberOfParameters() === 0)
		{
			throw new SerializationException("Field {$this->target->name} has no parameters. Fields marked as setters must have at least one parameter.");
		}

		$paramCount = $this->target->getNumberOfRequiredParameters();

		if ($paramCount > 1)
		{
			throw new SerializationException("Field {$this->target->name} has {$paramCount} required parameters. Fields marked as setters must have one required parameter at most.");
		}
	}

	/**
	 * Determine if the method name has a prefix (get/set/is/has), and return that prefix.
	 *
	 * Returns an empty string if the method name does not have a prefix.
	 *
	 * @return string
	 */
	protected final function getMethodPrefix()
	{
		if ($this->isClassMethod() && preg_match(self::$GETTER_SETTER_REGEX, $this->target->name, $matches))
		{
			return $matches['prefix'];
		}

		return '';
	}
}
