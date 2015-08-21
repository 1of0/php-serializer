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

/**
 * @property SerializationContext       $context
 * @property MemberContext              $memberContext
 * @property ClassContext               $parentContext
 * @property string                     $name
 * @property bool                       $isArray
 * @property bool                       $isReference
 * @property bool                       $isIncluded
 * @property bool                       $serialize
 * @property bool                       $deserialize
 * @property CustomConverterInterface     $converter
 * @property mixed                      $value
 * @property SerializedMember           $serializedMember
 */
class Member
{
	const TYPE_PROPERTY = 0;
	const TYPE_METHOD = 1;

	private $context;
	private $memberContext;
	private $parentContext;
	private $name;
	private $isArray = false;
	private $isReference = false;
	private $isIncluded = true;
	private $serialize = true;
	private $deserialize = true;
	private $converter;
	private $value;
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
				$this->getType(),
				$this->parentContext->instance
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
			return null;
		}

		$value = $this->context->getMemberWalker()->serialize($value);
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
				$this->getType(),
				$arrayData
			));
			return;
		}

		if ($this->isReference)
		{
			$this->setInstanceValue($instance, $this->resolveReference());
			return;
		}

		$value = $this->serializedMember->value;
		$value = $this->context->getMemberWalker()->deserialize($value, $this->getType());

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
		if ($this->value === null)
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
				"Property {$this->name} in class {$this->parentContext->class->name} is marked as a reference, but " .
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
				"Property {$this->name} in class {$this->parentContext->class->name} is marked as a reference, but " .
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
	 * Determine the initial value (if an instance was provided in the parent context).
	 */
	private function determineValue()
	{
		if ($this->parentContext->instance === null)
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