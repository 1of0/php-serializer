<?php


namespace OneOfZero\Json\Internals\AnnotationHandlers;


use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\InclusionStrategy;
use OneOfZero\Json\Annotations\JsonProperty;
use OneOfZero\Json\Annotations\Repository;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Internals\Member;
use OneOfZero\Json\ReferableInterface;
use ReflectionClass;

class JsonPropertyHandler extends AbstractHandler
{
	const ID_METADATA_TAG = 'id';
	const CLASS_METADATA_TAG = '@class';

	/**
	 * @return string
	 */
	public function handlesAnnotation()
	{
		return JsonProperty::class;
	}

	/**
	 * @param ReflectionClass $class
	 * @param Annotation|JsonProperty $annotation
	 * @param Member $member
	 * @return bool
	 * @throws SerializationException
	 */
	public function handleSerialization(ReflectionClass $class, Annotation $annotation, Member $member)
	{
		if ($this->getInclusionStrategy($class) == InclusionStrategy::EXPLICIT && !$annotation->serialize)
		{
			return false;
		}

		if ($annotation->name)
		{
			$member->propertyName = $annotation->name;
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

			/** @var ReferableInterface $value */

			// Store ID and class as serialized data
			// TODO: Use a type index and type hash?
			$member->serializationData = [
				self::CLASS_METADATA_TAG => $memberClass,
				self::ID_METADATA_TAG => $value->getId()
			];
		}

		return true;
	}

	/**
	 * @param ReflectionClass $class
	 * @param array $serializedData
	 * @param Annotation|JsonProperty $annotation
	 * @param Member $member
	 * @return bool
	 * @throws SerializationException
	 */
	public function handleDeserialization(ReflectionClass $class, array $serializedData, Annotation $annotation,
	                                      Member $member)
	{
		if ($this->getInclusionStrategy($class) == InclusionStrategy::IMPLICIT && !$annotation->deserialize)
		{
			return false;
		}

		if ($annotation->name)
		{
			$member->propertyName = $annotation->name;
		}

		if (array_key_exists($member->getPropertyName(), $serializedData) && $annotation->isReference)
		{
			// Check for ID and class in serialized data
			// TODO: Use a type index and type hash?
			if (!array_key_exists(self::CLASS_METADATA_TAG, $serializedData[$member->getPropertyName()])
			||  !array_key_exists(self::ID_METADATA_TAG, $serializedData[$member->getPropertyName()]))
			{
				throw new SerializationException(
					"Property {$member->name} in class {$class->name} is marked as a reference, but the " .
					"serialized data does not contain a valid reference"
				);
			}

			// Load ID and class from serialized data
			$id = $serializedData[$member->getPropertyName()][self::ID_METADATA_TAG];
			$memberClass = $serializedData[$member->getPropertyName()][self::CLASS_METADATA_TAG];

			// Load @Repository annotation
			/** @var Repository $repositoryAnnotation */
			$repositoryAnnotation = $this->annotationReader->getClassAnnotation(
				new ReflectionClass($memberClass),
				Repository::class
			);

			// Does the value's class have a @Repository annotation?
			if (!$repositoryAnnotation)
			{
				throw new SerializationException(
					"Property {$member->name} in class {$class->name} is marked as a reference, but does " .
					"not specify a @Repository attribute"
				);
			}

			// Check for repository existence
			if (!class_exists($repositoryAnnotation->class))
			{
				throw new SerializationException(
					"The repository specified on the {$memberClass} class can not be found"
				);
			}

			// Load instance from repository
			$object = call_user_func([ $repositoryAnnotation->class, 'get' ], $id);

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
			return $strategyAnnotation->strategy;
		}

		return $this->configuration->defaultInclusionStrategy;
	}
}