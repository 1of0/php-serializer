<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

trait BaseMapperTrait
{
	/**
	 * Holds the base mapper.
	 *
	 * @var MapperInterface|ObjectMapperInterface|MemberMapperInterface $base
	 */
	protected $base;

	/**
	 * Holds the reflection target.
	 *
	 * @var ReflectionClass|ReflectionProperty|ReflectionMethod $target
	 */
	protected $target;
	
	/**
	 * {@inheritdoc}
	 */
	public final function getBase()
	{
		return $this->base;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function setBase(MapperInterface $base)
	{
		$this->base = $base;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getTarget()
	{
		return $this->target;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function setTarget($target)
	{
		$this->target = $target;
	}
}
