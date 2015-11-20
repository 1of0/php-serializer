<?php

namespace OneOfZero\Json\Internals\Mappers;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use OneOfZero\Json\Annotations;
use ReflectionMethod;
use ReflectionProperty;

class AnnotationFieldMapper extends AbstractFieldMapper
{
	/**
	 * @var AnnotationReader $reader
	 */
	private $reader;

	public function __construct(AnnotationReader $reader)
	{
		$this->reader = $reader;
	}

	/**
	 *
	 */
	public function map()
	{
		// Run phpDocumentor parser here; annotations override

		$this->isArray = $this->hasAnnotation(Annotations\IsArray::class);

		/** @var Annotations\Type $typeAnnotation */
		$typeAnnotation = $this->getAnnotation(Annotations\Type::class);
		if ($typeAnnotation)
		{
			$this->hasType;
			$this->type = $typeAnnotation->value;
		}

		$this->isGetter = $this->hasAnnotation(Annotations\Getter::class);
		$this->isSetter = $this->hasAnnotation(Annotations\Setter::class);
		$this->hasIgnore = $this->hasAnnotation(Annotations\Ignore::class);

		/** @var Annotations\AbstractName $nameAnnotation */
		$nameAnnotation = $this->getAnnotation(Annotations\AbstractName::class);
		if ($nameAnnotation)
		{
			$this->name = $nameAnnotation->name;
		}

		/** @var Annotations\Property $propertyAnnotation */
		$propertyAnnotation = $this->getAnnotation(Annotations\Property::class);
		if ($propertyAnnotation)
		{
			$this->isProperty = true;
			$this->serialize = $propertyAnnotation->serialize;
			$this->deserialize = $propertyAnnotation->deserialize;
		}

		/** @var Annotations\IsReference $referenceAnnotation */
		$referenceAnnotation = $this->getAnnotation(Annotations\IsReference::class);
		if ($referenceAnnotation)
		{
			$this->isReference = true;
			$this->isReferenceLazy = $referenceAnnotation->lazy;
		}

		/** @var Annotations\CustomConverter $converterAnnotation */
		$converterAnnotation = $this->getAnnotation(Annotations\CustomConverter::class);
		if ($converterAnnotation)
		{
			$this->hasCustomConverter = true;
			$this->customConverterClass = $converterAnnotation->value;
			$this->customConverterSerializes = $converterAnnotation->serialize;
			$this->customConverterDeserializes = $converterAnnotation->deserialize;
		}
	}

	/**
	 * @param string $annotationClass
	 * @return bool
	 */
	private function hasAnnotation($annotationClass)
	{
		return $this->getAnnotation($annotationClass) !== null;
	}

	/**
	 * @param string $annotationClass
	 * @return Annotation|null
	 */
	private function getAnnotation($annotationClass)
	{
		if ($this->target instanceof ReflectionProperty)
		{
			return $this->reader->getPropertyAnnotation($this->target, $annotationClass);
		}

		if ($this->target instanceof ReflectionMethod)
		{
			return $this->reader->getMethodAnnotation($this->target, $annotationClass);
		}

		return null;
	}
}

