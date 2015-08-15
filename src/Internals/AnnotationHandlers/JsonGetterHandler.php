<?php


namespace OneOfZero\Json\Internals\AnnotationHandlers;


use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\JsonGetter;
use OneOfZero\Json\Internals\Member;
use ReflectionClass;
use stdClass;

class JsonGetterHandler extends AbstractHandler
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
		if ($member->type == Member::TYPE_METHOD && !$member->getAnnotation(JsonGetter::class))
		{
			return false;
		}
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
		return true;
	}
}