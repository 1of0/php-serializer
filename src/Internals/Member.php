<?php


namespace OneOfZero\Json\Internals;


use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\AbstractName;
use OneOfZero\Json\Annotations\CustomConverter;
use OneOfZero\Json\Annotations\ExplicitInclusion;
use OneOfZero\Json\Annotations\Getter;
use OneOfZero\Json\Annotations\Ignore;
use OneOfZero\Json\Annotations\IsArray;
use OneOfZero\Json\Annotations\IsReference;
use OneOfZero\Json\Annotations\Property;
use OneOfZero\Json\Annotations\Repository;
use OneOfZero\Json\Annotations\Setter;
use OneOfZero\Json\Annotations\Type;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\JsonConverterInterface;
use OneOfZero\Json\ReferableInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class Member
{
	const TYPE_PROPERTY = 0;
	const TYPE_METHOD = 1;

	/**
	 * @var SerializationContext $context
	 */
	private $context;

	/**
	 * @var MemberContext $memberContext
	 */
	private $memberContext;

	/**
	 * @var ClassContext $parentContext
	 */
	private $parentContext;

	/**
	 * @var string $name
	 */
	private $name;

	/**
	 * @var bool $isArray
	 */
	private $isArray = false;

	/**
	 * @var bool $isReference
	 */
	private $isReference = false;

	/**
	 * @var bool $isIncluded
	 */
	private $isIncluded = true;

	/**
	 * @var bool $serialize
	 */
	private $serialize = true;

	/**
	 * @var bool $deserialize
	 */
	private $deserialize = true;

	/**
	 * @var JsonConverterInterface $converter
	 */
	private $converter;

	/**
	 * @var mixed $value
	 */
	private $value;

	/**
	 * @var SerializedMember $serializedMember
	 */
	private $serializedMember;

	/**
	 * @param SerializationContext $context
	 * @param ClassContext $parentContext
	 * @param MemberContext $memberContext
	 */
	public function __construct(SerializationContext $context, ClassContext $parentContext,
	                            MemberContext $memberContext)
	{
		$this->context = $context;
		$this->name = $memberContext->member->name;
		$this->parentContext = $parentContext;
		$this->memberContext = $memberContext;
		$this->serializedMember = new SerializedMember($this->name);
		$this->isArray = $this->hasAnnotation(IsArray::class);
		$this->isReference = $this->hasAnnotation(IsReference::class);

		$this->determineInclusion();
		$this->determineSerializationSupport();
		$this->determinePropertyName();
		$this->determineValue();
		$this->detectCustomConverter();
	}

	public function serialize()
	{
		if (!$this->isIncluded || !$this->serialize)
		{
			return null;
		}

		if ($this->isMethod() && !$this->hasAnnotation(Getter::class))
		{
			return null;
		}

		$value = null;
		$valueSet = false;

		if ($this->converter && $this->converter->canConvert($this->getType()))
		{
			$value = $this->converter->serialize(
				$this->value,
				$this->serializedMember->propertyName,
				$this->getType()
			);
			$valueSet = true;
		}

		if ($this->isReference)
		{
			$value = $this->getReference();
			$valueSet = true;
		}

		if (!$valueSet)
		{
			$value = $this->value;
		}

		if (!$this->context->configuration->includeNullValues && is_null($value))
		{
			return null;
		}

		$value = $this->context->memberWalker->serialize($value);
		$this->serializedMember->value = $value;

		return $this->serializedMember;
	}

	public function deserialize($serializedData, $instance)
	{
		if (!$this->isIncluded || !$this->deserialize)
		{
			return;
		}

		if ($this->isMethod() && !$this->hasAnnotation(Setter::class))
		{
			return;
		}

		$propertyName = $this->serializedMember->propertyName;
		if (!array_key_exists($propertyName, $serializedData))
		{
			return;
		}

		$arrayData = (array)$serializedData;
		$this->serializedMember->value = $arrayData[$propertyName];

		if ($this->converter && $this->converter->canConvert($this->getType()))
		{
			$this->setInstanceValue($instance, $this->converter->deserialize(
				$this->serializedMember->value,
				$this->serializedMember->propertyName,
				$this->getType()
			));
			return;
		}

		if ($this->isReference)
		{
			$this->setInstanceValue($instance, $this->resolveReference());
			return;
		}

		$value = $this->serializedMember->value;
		$value = $this->context->memberWalker->deserialize($value, $this->getType());

		$this->setInstanceValue($instance, $value);
	}

	private function setInstanceValue($instance, $value)
	{
		if ($this->isProperty())
		{
			$this->memberContext->member->setValue($instance, $value);
		}
		else
		{
			$this->memberContext->member->invoke($instance, $value);
		}
	}

	/**
	 * @return array|null
	 * @throws SerializationException
	 */
	private function getReference()
	{
		if (is_null($this->value))
		{
			return null;
		}

		if (!($this->value instanceof ReferableInterface))
		{
			throw new SerializationException(
				"Property {$this->name} in class {$this->parentContext->class->name} is marked as a reference, but " .
				"does not implement ReferableInterface"
			);
		}

		$this->validateReference();

		$reference = [];
		Metadata::set($reference, Metadata::TYPE, $this->getType());
		Metadata::set($reference, Metadata::ID, $this->value->getId());
		return $reference;
	}

	/**
	 * Attempts to resolve a reference from the serialized member data.
	 * @return mixed|null
	 * @throws SerializationException
	 */
	private function resolveReference()
	{
		if (is_null($this->serializedMember->value))
		{
			return null;
		}

		$this->validateReference();

		$referenceId = $this->serializedMember->getMetadata(Metadata::ID);
		if (is_null($referenceId))
		{
			throw new SerializationException(
				"Property {$this->name} in class {$this->parentContext->class->name} is marked as a reference, but " .
				"the serialized data does not contain a valid reference"
			);
		}

		// Load @Repository annotation
		$referenceContext = new ClassContext($this->context, new ReflectionClass($this->getType()));
		$repositoryAnnotation = $referenceContext->getAnnotation(Repository::class);

		// Load instance from repository
		$object = call_user_func([ $repositoryAnnotation->value, 'get' ], $referenceId);

		return $object;
	}

	/**
	 * Assuming the member is marked as a reference, this method will validate the requirements for a reference.
	 * @throws SerializationException
	 */
	private function validateReference()
	{
		$type = $this->getType();

		if (is_null($type))
		{
			throw new SerializationException(
				"Property {$this->name} in class {$this->parentContext->class->name} is marked as a reference, but " .
				"does not specify or imply a valid type"
			);
		}

		$referenceContext = new ClassContext($this->context, new ReflectionClass($type));
		$repositoryAnnotation = $referenceContext->getAnnotation(Repository::class);
		if (!$repositoryAnnotation)
		{
			throw new SerializationException(
				"Property {$this->name} in class {$this->parentContext->class->name} is marked as a reference, but " .
				"the type {$type} does not specify a @Repository annotation"
			);
		}

		// Check for repository existence
		if (!class_exists($repositoryAnnotation->value))
		{
			throw new SerializationException(
				"The repository specified on the {$referenceContext->class->name} class can not be found"
			);
		}
	}

	/**
	 * Determine whether or not the member should be included in the serialization/deserialization.
	 */
	private function determineInclusion()
	{
		// When @ExplicitInclusion is defined on the parent class, by default members are not included
		$this->isIncluded = !$this->parentContext->hasAnnotation(ExplicitInclusion::class);

		// @Property, @Getter, and @Setter mark inclusion, even when @ExplicitInclusion is defined on the parent class
		if ($this->hasAnnotation(AbstractName::class))
		{
			$this->isIncluded = true;
		}

		// When @Ignore is defined on the member, the member is not included
		if ($this->hasAnnotation(Ignore::class))
		{
			$this->isIncluded = false;
		}
	}

	/**
	 * Determine whether or not the member supports serialization or deserialization specifically.
	 */
	private function determineSerializationSupport()
	{
		/** @var Property $propertyAnnotation */
		$propertyAnnotation = $this->getAnnotation(Property::class);
		if ($propertyAnnotation)
		{
			$this->serialize = $propertyAnnotation->serialize;
			$this->deserialize = $propertyAnnotation->deserialize;
		}

		/** @var Ignore $ignoreAnnotation */
		$ignoreAnnotation = $this->getAnnotation(Ignore::class);
		if ($ignoreAnnotation)
		{
			$this->serialize = !$ignoreAnnotation->ignoreOnSerialize;
			$this->deserialize = !$ignoreAnnotation->ignoreOnDeserialize;
		}
	}

	/**
	 * Determine property name.
	 */
	private function determinePropertyName()
	{
		// @Property, @Getter, and @Setter extend AbstractName and may provide a custom name

		/** @var AbstractName $nameAnnotation */
		$nameAnnotation = $this->getAnnotation(AbstractName::class);

		if ($nameAnnotation)
		{
			$this->serializedMember->propertyName = $nameAnnotation->name;
		}
	}

	/**
	 * Determine the initial value (if an instance was provided in the parent context).
	 */
	private function determineValue()
	{
		if (is_null($this->parentContext->instance))
		{
			return;
		}

		if ($this->isProperty())
		{
			$this->value = $this->memberContext->member->getValue($this->parentContext->instance);
		}

		if ($this->isMethod() && $this->hasAnnotation(Getter::class))
		{
			$this->value = $this->memberContext->member->invoke($this->parentContext->instance);
		}
	}

	/**
	 * Detect whether the member has a custom converter defined
	 */
	private function detectCustomConverter()
	{
		$converterAnnotation = $this->getAnnotation(CustomConverter::class);
		if ($converterAnnotation)
		{
			$converterClass = $converterAnnotation->value;
			$this->converter = new $converterClass();
		}
	}

	/**
	 * Determine property type.
	 */
	private function getType()
	{
		/** @var Type $typeAnnotation */
		$typeAnnotation = $this->getAnnotation(Type::class);

		if ($typeAnnotation)
		{
			// From annotation
			return $typeAnnotation->value;
		}

		if (is_object($this->value))
		{
			// From value (get_class)
			return get_class($this->value);
		}

		if ($this->serializedMember->containsMetadata(Metadata::TYPE))
		{
			// From metadata (@class member in serialized data)
			return $this->serializedMember->getMetadata(Metadata::TYPE);
		}

		return null;
	}

	/**
	 * @return bool
	 */
	private function isProperty()
	{
		return $this->memberContext->member instanceof ReflectionProperty;
	}

	/**
	 * @return bool
	 */
	private function isMethod()
	{
		return $this->memberContext->member instanceof ReflectionMethod;
	}

	/**
	 * @param string $annotationClass
	 * @return bool
	 */
	private function hasAnnotation($annotationClass)
	{
		return $this->memberContext->hasAnnotation($annotationClass);
	}

	/**
	 * @param string $annotationClass
	 * @return Annotation|null
	 */
	private function getAnnotation($annotationClass)
	{
		return $this->memberContext->getAnnotation($annotationClass);
	}

	/**
	 * @param string|null $annotationClass
	 * @return Annotation[]
	 */
	private function getAnnotations($annotationClass = null)
	{
		return $this->memberContext->getAnnotations($annotationClass);
	}
}