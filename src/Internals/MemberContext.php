<?php


namespace OneOfZero\Json\Internals;


use Doctrine\Common\Annotations\Annotation;
use ReflectionMethod;
use ReflectionProperty;

class MemberContext
{
	use AnnotationHelperTrait;

	/**
	 * @var ReflectionProperty|ReflectionMethod $class
	 */
	public $member;

	/**
	 * @param SerializationContext $context
	 * @param ReflectionProperty|ReflectionMethod $member
	 */
	public function __construct(SerializationContext $context, $member)
	{
		$this->member = $member;

		if ($member instanceof ReflectionProperty)
		{
			$this->annotations = $context->annotationReader->getPropertyAnnotations($member);
		}

		if ($member instanceof ReflectionMethod)
		{
			$this->annotations = $context->annotationReader->getMethodAnnotations($member);
		}
	}
}