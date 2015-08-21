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
 * Class ClassContext
 * @package OneOfZero\Json\Internals
 *
 * Provides a reflection context of a class, and provides helper methods to obtain the annotations of the relevant
 * class.
 */
class ClassContext
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
	 * @var object|null $instance
	 */
	public $instance;

	/**
	 * @param SerializationContext $context
	 * @param ReflectionClass $class
	 * @param object|null $instance
	 */
	public function __construct(SerializationContext $context, ReflectionClass $class, $instance = null)
	{
		$this->class = $class;
		$this->instance = $instance;
		$this->annotations = $context->getAnnotationReader()->getClassAnnotations($class);
	}
}