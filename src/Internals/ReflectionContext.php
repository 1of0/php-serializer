<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * Provides a reflection context of a class, property, or method, and provides helper methods to obtain the annotations
 * of the relevant reflected type.
 */
class ReflectionContext
{
	/**
	 * Provides annotation helper methods.
	 */
	use AnnotationHelperTrait;

	/**
	 * @var ReflectionClass|ReflectionProperty|ReflectionMethod $reflector
	 */
	public $reflector;

	/**
	 * @param SerializerContext $context
	 * @param Reflector $reflector
	 */
	public function __construct(SerializerContext $context, Reflector $reflector)
	{
		$this->reflector = $reflector;

		if ($reflector instanceof ReflectionClass)
		{
			$this->annotations = $context->getAnnotationReader()->getClassAnnotations($reflector);
		}

		if ($reflector instanceof ReflectionProperty)
		{
			$this->annotations = $context->getAnnotationReader()->getPropertyAnnotations($reflector);
		}

		if ($reflector instanceof ReflectionMethod)
		{
			$this->annotations = $context->getAnnotationReader()->getMethodAnnotations($reflector);
		}
	}
}
