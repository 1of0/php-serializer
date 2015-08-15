<?php


namespace OneOfZero\Json\Internals\AnnotationHandlers;


use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Internals\Member;
use ReflectionClass;
use stdClass;

abstract class AbstractHandler
{
	/**
	 * @var Configuration $configuration
	 */
	protected $configuration;

	/**
	 * @var AnnotationReader $annotationReader
	 */
	protected $annotationReader;

	/**
	 * @param Configuration $configuration
	 * @param AnnotationReader $annotationReader
	 */
	public function __construct(Configuration $configuration, AnnotationReader $annotationReader)
	{
		$this->configuration = $configuration;
		$this->annotationReader = $annotationReader;
	}

	/**
	 * @return string
	 */
	public function targetAnnotation()
	{
		return null;
	}

	/**
	 * @return string[]
	 */
	public function dependsOn()
	{
		return [];
	}

	/**
	 * @param ReflectionClass $class
	 * @param Annotation $annotation
	 * @param Member $member
	 * @return bool
	 */
	public abstract function handleSerialization(ReflectionClass $class, $annotation, Member $member);

	/**
	 * @param ReflectionClass $class
	 * @param array|stdClass $deserializedData
	 * @param Annotation $annotation
	 * @param Member $member
	 * @return bool
	 */
	public abstract function handleDeserialization(ReflectionClass $class, $deserializedData, $annotation,
	                                               Member $member);

	/**
	 * @param ReflectionClass $class
	 * @param null|string $annotationClass
	 * @return Annotation[]
	 */
	protected function getClassAnnotations(ReflectionClass $class, $annotationClass = null)
	{
		$classAnnotations = $this->annotationReader->getClassAnnotation($class, $annotationClass);

		if (is_null($annotationClass))
		{
			return $classAnnotations;
		}

		$annotations = [];
		foreach ($classAnnotations as $annotation)
		{
			if (get_class($annotation) === $annotationClass)
			{
				$annotations[] = $annotation;
			}
		}
		return $annotations;
	}

	/**
	 * @param ReflectionClass $class
	 * @param string $annotationClass
	 * @return Annotation|null
	 */
	protected function getClassAnnotation(ReflectionClass $class, $annotationClass)
	{
		return $this->annotationReader->getClassAnnotation($class, $annotationClass);
	}
}