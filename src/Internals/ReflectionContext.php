<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;


use ReflectionClass;

/**
 * Class ReflectionContext
 * @package OneOfZero\Json\Internals
 *
 * Provides a reflection context of a class, and provides helper methods to obtain the annotations of the relevant
 * class.
 */
class ReflectionContext
{
	/**
	 * Provides annotation helper methods.
	 */
	use AnnotationHelperTrait;

	/**
	 * @var ReflectionClass $class
	 */
	public $class;

	/**
	 * @param SerializerContext $context
	 * @param ReflectionClass $class
	 */
	public function __construct(SerializerContext $context, ReflectionClass $class)
	{
		$this->class = $class;
		$this->annotations = $context->getAnnotationReader()->getClassAnnotations($class);
	}
}