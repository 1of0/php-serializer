<?php


namespace OneOfZero\Json\Internals\AnnotationHandlers;


use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\InclusionStrategy;
use OneOfZero\Json\Annotations\JsonIgnore;
use OneOfZero\Json\Internals\Member;
use ReflectionClass;
use stdClass;

class JsonIgnoreHandler extends AbstractHandler
{
	/**
	 * @return string
	 */
	public function targetAnnotation()
	{
		return JsonIgnore::class;
	}

	/**
	 * @param ReflectionClass $class
	 * @param Annotation|JsonIgnore $annotation
	 * @param Member $member
	 * @return bool
	 */
	public function handleSerialization(ReflectionClass $class, $annotation, Member $member)
	{
		if ($this->getInclusionStrategy($class) == InclusionStrategy::IMPLICIT && $annotation->ignoreOnSerialize)
		{
			return false;
		}
		return true;
	}

	/**
	 * @param ReflectionClass $class
	 * @param array|stdClass $deserializedData
	 * @param Annotation|JsonIgnore $annotation
	 * @param Member $member
	 * @return bool
	 */
	public function handleDeserialization(ReflectionClass $class, $deserializedData, $annotation, Member $member)
	{
		if ($this->getInclusionStrategy($class) == InclusionStrategy::IMPLICIT && $annotation->ignoreOnDeserialize)
		{
			return false;
		}
		return true;
	}

	/**
	 * @param ReflectionClass $class
	 * @return int
	 */
	private function getInclusionStrategy(ReflectionClass $class)
	{
		/** @var InclusionStrategy $strategyAnnotation */
		$strategyAnnotation = $this->getClassAnnotation($class, InclusionStrategy::class);
		if ($strategyAnnotation)
		{
			return $strategyAnnotation->value;
		}

		return $this->configuration->defaultInclusionStrategy;
	}
}