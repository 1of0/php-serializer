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
use OneOfZero\Json\Configuration;
use OneOfZero\Json\CustomMemberConverterInterface;
use OneOfZero\Json\Exceptions\ReferenceException;
use OneOfZero\Json\Exceptions\ResumeSerializationException;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\NameFilterInterface;
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
	 * @var bool $lazyResolution
	 */
	private $lazyResolution = true;

	/**
	 * @var bool $serialize
	 */
	private $serialize = true;

	/**
	 * @var bool $deserialize
	 */
	private $deserialize = true;

	/**
	 * @var CustomMemberConverterInterface $converter
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
		$this->serializedMember = new SerializedMember();
		$this->isArray = $this->hasAnnotation(IsArray::class);

		$this->determineInclusion();
		$this->determineReferenceConfiguration();
		$this->determineSerializationSupport();
		$this->determinePropertyName();
		$this->detectCustomConverter();
	}

	/**
	 * @param object $parentInstance
	 * @param array $serializedParent
	 * @throws ReferenceException
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
		$type = $this->getSerializationType($this->value);

		if ($this->converter && $this->converter->canConvert($type))
		{
			try
			{
				$value = $this->converter->serialize(
					$this->value,
					$this->serializedMember->propertyName,
					$type,
					new SerializationState($parentInstance, $serializedParent)
				);
				$valueSet = true;
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if ($this->isReference)
		{
			$value = $this->getReference($this->value);
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
	 * @throws ReferenceException
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

		$serializedValue = array_key_exists($propertyName, $serializedParent)
			? $serializedParent[$propertyName]
			: null
		;
		$serializedType = $this->getDeserializationType($serializedValue);

		$this->serializedMember->value = $serializedValue;

		if ($this->converter && $this->converter->canConvert($serializedType))
		{
			try
			{
				$this->setValue($parentInstance, $this->converter->deserialize(
					$serializedValue,
					$this->serializedMember->propertyName,
					$serializedType,
					new DeserializationState($serializedParent, $parentInstance)
				));
				return;
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if ($this->isReference)
		{
			$this->setValue($parentInstance, $this->resolveReference($serializedValue));
			return;
		}

		$value = $this->context->getMemberWalker()->deserialize($serializedValue, $serializedType);
		$this->setValue($parentInstance, $value);
	}

	/**
	 * @param mixed $value
	 * @param bool $isArrayItem
	 * @return array|null
	 * @throws ReferenceException
	 */
	private function getReference($value, $isArrayItem = false)
	{
		if ($value === null)
		{
			return null;
		}

		if (!$isArrayItem && $this->isArray)
		{
			if (!is_array($value))
			{
				throw new ReferenceException("Property {$this->name} in class {$this->parentContext->reflector->name} is marked as an array, but does not hold an array");
			}

			$references = [];
			foreach ($value as $item)
			{
				$references[] = $this->getReference($item, true);
			}
			return $references;
		}

		if (!($value instanceof ReferableInterface))
		{
			throw new ReferenceException("Property {$this->name} in class {$this->parentContext->reflector->name} is marked as a reference, but does not implement ReferableInterface");
		}

		$type = $this->getSerializationType($value);
		if ($type === null)
		{
			throw new ReferenceException("Property {$this->name} in class {$this->parentContext->reflector->name} is marked as a reference, but does not specify or imply a valid type");
		}

		$reference = [];
		Metadata::set($reference, Metadata::TYPE, $type);
		Metadata::set($reference, Metadata::ID, $value->getId());
		return $reference;
	}

	/**
	 * Attempts to resolve a reference from the serialized member data.
	 * @param mixed $serializedValue
	 * @param bool $isArrayItem
	 * @return mixed|null
	 * @throws ReferenceException
	 */
	private function resolveReference($serializedValue, $isArrayItem = false)
	{
		if ($serializedValue === null)
		{
			return null;
		}

		if (!$isArrayItem && $this->isArray)
		{
			$array = [];
			foreach ($serializedValue as $item)
			{
				$array[] = $this->resolveReference($item, true);
			}
			return $array;
		}

		$type = $this->getDeserializationType($serializedValue);
		if ($type === null)
		{
			throw new ReferenceException("Property {$this->name} in class {$this->parentContext->reflector->name} is marked as a reference, but does not specify or imply a valid type");
		}

		$referenceClass = $type;
		$referenceId = Metadata::get($serializedValue, Metadata::ID);
		if ($referenceId === null)
		{
			throw new ReferenceException("Property {$this->name} in class {$this->parentContext->reflector->name} is marked as a reference, but the serialized data does not contain a valid reference");
		}

		$referenceResolver = $this->context->getReferenceResolver();
		if (!$referenceResolver)
		{
			throw new ReferenceException("Could not load the reference resolver from the container");
		}

		// Resolve reference
		$object = $this->context->getReferenceResolver()->resolve($referenceClass, $referenceId, $this->lazyResolution);

		return $object;
	}

	/**
	 * Determine whether or not the member should be included in the serialization/deserialization.
	 */
	private function determineInclusion()
	{
		// When @ExplicitInclusion is defined on the parent class, by default members are not included
		$this->isIncluded = !$this->parentContext->hasAnnotation(ExplicitInclusion::class);

		// If the member is not public, it will be excluded by default
		if (!$this->memberContext->reflector->isPublic())
		{
			$this->isIncluded = false;
		}

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
	 * Determine whether or not the member is marked as a reference, and determine the reference resolution type.
	 */
	private function determineReferenceConfiguration()
	{
		$referenceAnnotation = $this->getAnnotation(IsReference::class);

		$this->isReference = (bool)$referenceAnnotation;

		if ($referenceAnnotation && $referenceAnnotation->value !== null)
		{
			$this->lazyResolution = $referenceAnnotation->value;
		}
		else
		{
			$defaultResolutionType = $this->context->getConfiguration()->defaultResolutionType;
			$this->lazyResolution = ($defaultResolutionType === Configuration::RESOLVE_LAZY);
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
		/** @var AbstractName $nameAnnotation */

		// By default assume the member's name
		$name = $this->name;

		// But trim, get, set, and is if the member is a getter or setter
		if ($this->isGetter() || $this->isSetter())
		{
			$name = lcfirst(preg_replace('/^(get|set|is)/', '', $name));
		}

		// @Property, @Getter, and @Setter extend AbstractName and may provide a custom name
		$nameAnnotation = $this->getAnnotation(AbstractName::class);
		if ($nameAnnotation && $nameAnnotation->name)
		{
			$name = $nameAnnotation->name;
		}

		// If a name filter is configured, determine the serialized name for this member
		$nameFilter = $this->getNameFilter();
		if ($nameFilter !== null)
		{
			$name = $nameFilter->getSerializedName($name);
		}

		$this->serializedMember->propertyName = $name;
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

		$isPublic = $this->memberContext->reflector->isPublic();
		$this->memberContext->reflector->setAccessible(true);

		if ($this->isProperty())
		{
			$this->value = $this->memberContext->reflector->getValue($parentInstance);
		}

		if ($this->isGetter())
		{
			$this->value = $this->memberContext->reflector->invoke($parentInstance);
		}

		if (!$isPublic)
		{
			$this->memberContext->reflector->setAccessible(false);
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
		if ($instance === null)
		{
			return;
		}

		$isPublic = $this->memberContext->reflector->isPublic();
		$this->memberContext->reflector->setAccessible(true);

		if ($this->isProperty())
		{
			$this->memberContext->reflector->setValue($instance, $value);
		}

		if ($this->isSetter())
		{
			$this->memberContext->reflector->invoke($instance, $value);
		}

		if (!$isPublic)
		{
			$this->memberContext->reflector->setAccessible(false);
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
			$this->converter = $this->context->getInstance($converterAnnotation->value);
		}
	}

	/**
	 * Determine property type.
	 * @param null $value
	 * @return mixed|null|string
	 */
	private function getSerializationType($value)
	{
		// Attempt resolution from @Type annotation
		if ($this->hasAnnotation(Type::class))
		{
			return $this->getAnnotation(Type::class)->value;
		}

		// Attempt resolution from value class
		if (is_object($value))
		{
			return get_class($value);
		}

		return null;
	}

	private function getDeserializationType($serializedValue)
	{
		// Attempt resolution from @Type annotation
		if ($this->hasAnnotation(Type::class))
		{
			return $this->getAnnotation(Type::class)->value;
		}

		// Attempt resolution from metadata (@class member in serialized data)
		if (Metadata::contains($serializedValue, Metadata::TYPE))
		{
			return Metadata::get($serializedValue, Metadata::TYPE);
		}

		return null;
	}

	/**
	 * @return NameFilterInterface|null
	 * @throws SerializationException
	 */
	private function getNameFilter()
	{
		$nameFilterClass = $this->context->getConfiguration()->nameFilterClass;
		if ($nameFilterClass === null)
		{
			return null;
		}

		if (!class_exists($nameFilterClass))
		{
			throw new SerializationException("Class \"$nameFilterClass\" does not exist");
		}

		if (!in_array(NameFilterInterface::class, class_implements($nameFilterClass)))
		{
			throw new SerializationException("Class \"$nameFilterClass\" does not implement NameFilterInterface");
		}

		return new $nameFilterClass();
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
	 * @return bool
	 */
	private function isGetter()
	{
		return $this->isMethod()
		    && $this->hasAnnotation(Getter::class)
		    && $this->memberContext->reflector->getNumberOfRequiredParameters() == 0
		;
	}

	/**
	 * @return bool
	 */
	private function isSetter()
	{
		return $this->isMethod()
		    && $this->hasAnnotation(Setter::class)
		    && $this->memberContext->reflector->getNumberOfRequiredParameters() == 1
		;
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
