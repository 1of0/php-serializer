<?php


namespace OneOfZero\Json\Internals\AnnotationHandlers;


use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\JsonConverter;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Internals\Member;
use OneOfZero\Json\JsonConverterInterface;
use ReflectionClass;
use stdClass;

class JsonConverterHandler extends AbstractHandler
{
	/**
	 * @var JsonConverterInterface[] $converters
	 */
	private $converters = [];

	/**
	 * @return string
	 */
	public function targetAnnotation()
	{
		return JsonConverter::class;
	}

	/**
	 * @return string[]
	 */
	public function dependsOn()
	{
		return [ JsonPropertyHandler::class, JsonGetterHandler::class, JsonSetterHandler::class ];
	}

	/**
	 * @param ReflectionClass $class
	 * @param Annotation $annotation
	 * @param Member $member
	 * @return bool
	 * @throws SerializationException
	 */
	public function handleSerialization(ReflectionClass $class, $annotation, Member $member)
	{
		/** @var JsonConverter $annotation */

		if (!$annotation->serialize)
		{
			return true;
		}

		$converterClass = $annotation->value;

		$this->ensureClassExists($converterClass);

		$converterInstance = array_key_exists($converterClass, $this->converters)
			? $this->converters[$converterClass]
			: new $converterClass()
		;

		if ($converterInstance->canConvert($member->getObjectClass()))
		{
			$member->serializationData = $converterInstance->serialize(
				$member->value,
				$member->getPropertyName(),
				$member->getObjectClass()
			);
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
		/** @var JsonConverter $annotation */

		if (!$annotation->deserialize)
		{
			return true;
		}

		$converterClass = $annotation->value;

		$this->ensureClassExists($converterClass);

		$converterInstance = array_key_exists($converterClass, $this->converters)
			? $this->converters[$converterClass]
			: new $converterClass()
		;

		if ($converterInstance->canConvert($member->getObjectClass()))
		{
			$member->value = $converterInstance->deserialize(
				$member->serializationData,
				$member->getPropertyName(),
				$member->getObjectClass()
			);
		}

		return true;
	}

	/**
	 * @param string $class
	 * @throws SerializationException
	 */
	private function ensureClassExists($class)
	{
		if (!class_exists($class))
		{
			throw new SerializationException(
				"Invalid class reference in @JsonConverter annotation; could not find class {$class}"
			);
		}
	}
}