<?php


namespace OneOfZero\Json\Internals;


use Doctrine\Common\Annotations\Annotation;
use ReflectionClass;

class ClassContext
{
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
		$this->annotations = $context->annotationReader->getClassAnnotations($class);
	}
}