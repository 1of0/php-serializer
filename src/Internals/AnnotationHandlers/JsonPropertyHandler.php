<?php


namespace OneOfZero\Json\Internals\AnnotationHandlers;


use ArrayObject;
use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\InclusionStrategy;
use OneOfZero\Json\Annotations\JsonIgnore;
use OneOfZero\Json\Annotations\JsonProperty;
use OneOfZero\Json\Annotations\Repository;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Internals\Member;
use OneOfZero\Json\ReferableInterface;
use ReflectionClass;
use stdClass;

class JsonPropertyHandler extends AbstractHandler
{
	const ID_TAG = 'id';
	const CLASS_TAG = '@class';

	/**
	 * @return string
	 */
	public function targetAnnotation()
	{
		return JsonProperty::class;
	}

	/**
	 * @return string[]
	 */
	public function dependsOn()
	{
		return [ JsonIgnoreHandler::class ];
	}

	/**
	 * @param ReflectionClass $class
	 * @param Annotation|JsonProperty $annotation
	 * @param Member $member
	 * @return bool
	 * @throws SerializationException
	 */
	public function handleSerialization(ReflectionClass $class, $annotation, Member $member)
	{
		/** @var ReferableInterface $value */

		if ($this->getInclusionStrategy($class) == InclusionStrategy::EXPLICIT && !$annotation->serialize)
		{
			return false;
		}

		if ($annotation->value)
		{
			$member->propertyName = $annotation->value;
		}

		$value = $member->value;
		if (!is_null($value) && $annotation->isReference)
		{
			$memberClass = $member->getObjectClass();

			// Does the value's class implement the ReferableInterface class?
			if (is_null($memberClass) || !in_array(ReferableInterface::class, class_implements($memberClass)))
			{
				throw new SerializationException(
					"Property {$member->name} in class {$class->name} is marked as a reference, but does " .
					"not implement ReferableInterface"
				);
			}

			// Does the value's class have a @Repository annotation?
			if (!$this->getClassAnnotation(new ReflectionClass($memberClass), Repository::class))
			{
				throw new SerializationException(
					"Property {$member->name} in class {$class->name} is marked as a reference, but does " .
					"not specify a @Repository attribute"
				);
			}

			// Store ID and class as serialized data
			// TODO: Use a type index and type hash?
			$member->serializationData = [
				self::CLASS_TAG => $memberClass,
				self::ID_TAG => $value->getId()
			];
		}

		return true;
	}

	/**
	 * @param ReflectionClass $class
	 * @param array|stdClass $deserializedData
	 * @param Annotation|JsonProperty $annotation
	 * @param Member $member
	 * @return bool
	 * @throws SerializationException
	 */
	public function handleDeserialization(ReflectionClass $class, $deserializedData, $annotation, Member $member)
	{
		/** @var Repository $repositoryAnnotation */

		if ($this->getInclusionStrategy($class) == InclusionStrategy::IMPLICIT && !$annotation->deserialize)
		{
			return false;
		}

		if ($annotation->value)
		{
			$member->propertyName = $annotation->value;
		}

		if (array_key_exists($member->getPropertyName(), $deserializedData) && $annotation->isReference)
		{
			/** @var ArrayObject $memberValue */
			$memberValue = (object)$deserializedData->{$member->getPropertyName()};

			// Check for ID and class in serialized data
			// TODO: Use a type index and type hash?
			if (!array_key_exists(self::CLASS_TAG, $memberValue)
			||  !array_key_exists(self::ID_TAG, $memberValue))
			{
				throw new SerializationException(
					"Property {$member->name} in class {$class->name} is marked as a reference, but the " .
					"serialized data does not contain a valid reference"
				);
			}

			// Load ID and class from serialized data
			$id = $memberValue->{self::ID_TAG};
			$memberClass = $memberValue->{self::CLASS_TAG};

			// Load @Repository annotation
			$repositoryAnnotation = $this->getClassAnnotation(new ReflectionClass($memberClass), Repository::class);

			// Does the value's class have a @Repository annotation?
			if (!$repositoryAnnotation)
			{
				throw new SerializationException(
					"Property {$member->name} in class {$class->name} is marked as a reference, but does " .
					"not specify a @Repository attribute"
				);
			}

			// Check for repository existence
			if (!class_exists($repositoryAnnotation->value))
			{
				throw new SerializationException(
					"The repository specified on the {$memberClass} class can not be found"
				);
			}

			// Load instance from repository
			$object = call_user_func([ $repositoryAnnotation->value, 'get' ], $id);

			// Set member value
			$member->value = $object;
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