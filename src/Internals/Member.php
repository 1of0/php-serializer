<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

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
use OneOfZero\Json\Annotations\Setter;
use OneOfZero\Json\Annotations\Type;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\CustomConverterInterface;
use OneOfZero\Json\ReferableInterface;
use ReflectionMethod;
use ReflectionProperty;

class Member
{
	const TYPE_PROPERTY = 0;
	const TYPE_METHOD = 1;

	/**
	 * @var SerializerContext $context
	 */
	private $context;

	/**
	 * @var ReflectionContext $memberContext
	 */
	private $memberContext;

	/**
	 * @var ReflectionContext $parentContext
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
	 * @var CustomConverterInterface $converter
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
	 * @param SerializerContext $context
	 * @param ReflectionContext $parentContext
	 * @param ReflectionContext $memberContext
	 */
	public function __construct(SerializerContext $context, ReflectionContext $parentContext,
	                            ReflectionContext $memberContext)
	{
		$this->context = $context;
		$this->name = $memberContext->reflector->name;
		$this->parentContext = $parentContext;
		$this->memberContext = $memberContext;
		$this->serializedMember = new SerializedMember($this->name);
		$this->isArray = $this->hasAnnotation(IsArray::class);
		$this->isReference = $this->hasAnnotation(IsReference::class);

		$this->determineInclusion();
		$this->determineSerializationSupport();
		$this->determinePropertyName();
		$this->detectCustomConverter();
	}

	/**
	 * @param object $parentInstance
	 * @param array $serializedParent
	 * @throws SerializationException
	 */
	public function serialize($parentInstance, array &$serializedParent)
	{
		if (!$this->isIncluded || !$this->serialize)
		{
			return;
		}

		if ($this->isMethod() && !$this->hasAnnotation(Getter::class))
		{
			return;
		}

		$this->loadValue($parentInstance);

		$value = null;
		$valueSet = false;

		if ($this->converter && $this->converter->canConvert($this->getType()))
		{
			$value = $this->converter->serialize(
				$this->value,
				$this->serializedMember->propertyName,
				$this->getType(),
				new SerializationState($parentInstance, $serializedParent)
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

		if (!$this->context->getConfiguration()->includeNullValues && $value === null)
		{
			return;
		}

		$value = $this->context->getMemberWalker()->serialize($value);
		$this->serializedMember->value = $value;

		$serializedParent[$this->serializedMember->propertyName] = $this->serializedMember->value;
	}

	/**
	 * @param array $serializedParent
	 * @param object $parentInstance
	 * @throws SerializationException
	 */
	public function deserialize(array $serializedParent, $parentInstance)
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
		if (!array_key_exists($propertyName, $serializedParent))
		{
			return;
		}

		$this->serializedMember->value = $serializedParent[$propertyName];

		if ($this->converter && $this->converter->canConvert($this->getType()))
		{
			$this->setValue($parentInstance, $this->converter->deserialize(
				$this->serializedMember->value,
				$this->serializedMember->propertyName,
				$this->getType(),
				new DeserializationState($serializedParent, $parentInstance)
			));
			return;
		}

		if ($this->isReference)
		{
			$this->setValue($parentInstance, $this->resolveReference());
			return;
		}

		$value = $this->serializedMember->value;
		$value = $this->context->getMemberWalker()->deserialize($value, $this->getType());

		$this->setValue($parentInstance, $value);
	}

	/**
	 * @return array|null
	 * @throws SerializationException
	 */
	private function getReference()
	{
		if ($this->value === null)
		{
			return null;
		}

		if (!($this->value instanceof ReferableInterface))
		{
			throw new SerializationException(
				"Property {$this->name} in class {$this->parentContext->reflector->name} is marked as a reference, but " .
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
		if ($this->serializedMember->value === null)
		{
			return null;
		}

		$this->validateReference();

		$referenceClass = $this->getType();
		$referenceId = $this->serializedMember->getMetadata(Metadata::ID);
		if ($referenceId === null)
		{
			throw new SerializationException(
				"Property {$this->name} in class {$this->parentContext->reflector->name} is marked as a reference, but " .
				"the serialized data does not contain a valid reference"
			);
		}

		$referenceResolver = $this->context->getReferenceResolver();
		if (!$referenceResolver)
		{
			throw new SerializationException("Could not load the reference resolver from the container");
		}

		// Resolve reference
		$object = $this->context->getReferenceResolver()->resolve($referenceClass, $referenceId);

		return $object;
	}

	/**
	 * Assuming the member is marked as a reference, this method will validate the requirements for a reference.
	 * @throws SerializationException
	 */
	private function validateReference()
	{
		if ($this->getType() === null)
		{
			throw new SerializationException(
				"Property {$this->name} in class {$this->parentContext->reflector->name} is marked as a reference, but " .
				"does not specify or imply a valid type"
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
	 * Load the value for this member given the $parentInstance
	 *
	 * @param object $parentInstance
	 */
	private function loadValue($parentInstance)
	{
		if ($parentInstance === null)
		{
			return;
		}

		if ($this->isProperty())
		{
			$this->value = $this->memberContext->reflector->getValue($parentInstance);
		}

		if ($this->isMethod() && $this->hasAnnotation(Getter::class))
		{
			$this->value = $this->memberContext->reflector->invoke($parentInstance);
		}
	}

	/**
	 * Set the value for this member given the $instance and the $value
	 *
	 * @param object $instance
	 * @param mixed $value
	 */
	private function setValue($instance, $value)
	{
		if ($this->isProperty())
		{
			$this->memberContext->reflector->setValue($instance, $value);
		}
		else
		{
			$this->memberContext->reflector->invoke($instance, $value);
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

			if ($this->context->getContainer() && $this->context->getContainer()->has($converterClass))
			{
				// Use converter instance from container, if available
				$this->converter = $this->context->getContainer()->get($converterClass);
			}
			else
			{
				// Otherwise manually instantiate
				// TODO: Check if class has public constructor (or no constructor)
				$this->converter = new $converterClass();
			}
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
		return $this->memberContext->reflector instanceof ReflectionProperty;
	}

	/**
	 * @return bool
	 */
	private function isMethod()
	{
		return $this->memberContext->reflector instanceof ReflectionMethod;
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