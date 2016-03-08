<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

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
	 * {@inh}
	 */
	public final function getConfiguration()
	{
		return $this->memberParent->getConfiguration();
	}

	/**
	 * {@inheritdoc}
	 */
	public final function setMemberParent(ObjectMapperInterface $memberParent)
	{
		$this->memberParent = $memberParent;
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
