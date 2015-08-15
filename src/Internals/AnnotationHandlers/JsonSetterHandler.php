<?php


namespace OneOfZero\Json\Internals\AnnotationHandlers;


use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\JsonSetter;
use OneOfZero\Json\Internals\Member;
use ReflectionClass;
use stdClass;

class JsonSetterHandler extends AbstractHandler
{
	/**
	 * @return string[]
	 */
	public function dependsOn()
	{
		return [ JsonPropertyHandler::class ];
	}

	/**
	 * @param ReflectionClass $class
	 * @param Annotation $annotation
	 * @param Member $member
	 * @return bool
	 */
	public function handleSerialization(ReflectionClass $class, $annotation, Member $member)
	{
		return true;
	}

	/**
	 * @param ReflectionClass $class
	 * @param array|stdClass $deserializedData
	 * @param Annotation $annotation
	 * @param Member $member
	 * @return bool
	 */
	public function handleDeserialization(ReflectionClass $class, $deserializedData, $annotation, Member $member)
	{
		if ($member->type == Member::TYPE_METHOD && !$member->getAnnotation(JsonSetter::class))
		{
			return false;
		}
		return true;
	}
}