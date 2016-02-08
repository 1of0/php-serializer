<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

use Doctrine\Common\Annotations\Annotation;
use OneOfZero\BetterAnnotations\Annotations;
use OneOfZero\Json\Annotations\AbstractName;
use OneOfZero\Json\Annotations\CustomConverter;
use OneOfZero\Json\Annotations\Getter;
use OneOfZero\Json\Annotations\Ignore;
use OneOfZero\Json\Annotations\IsArray;
use OneOfZero\Json\Annotations\IsReference;
use OneOfZero\Json\Annotations\Property;
use OneOfZero\Json\Annotations\Setter;
use OneOfZero\Json\Annotations\Type;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\PhpDocReader\PhpDocReader;
use ReflectionParameter;

class AnnotationFieldMapper extends AbstractFieldMapper
{
	/**
	 * @var Annotations $annotations
	 */
	private $annotations;

	/**
	 * @var PhpDocReader $docReader
	 */
	private $docReader;

	/**
	 * @var CustomConverter[] $customConverterAnnotations
	 */
	private $customConverterAnnotations = null;

	/**
	 * @param Annotations $annotations
	 */
	public function __construct(Annotations $annotations)
	{
		$this->annotations = $annotations;
		$this->docReader = new PhpDocReader(true);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		/** @var AbstractName $nameAnnotation */
		if ($nameAnnotation = $this->annotations->get($this->target, AbstractName::class))
		{
			return $nameAnnotation->name;
		}
		
		return parent::getName();
	}


	/**
	 * @return string|null
	 */
	public function getType()
	{
		// Try determining from @Type annotation
		if ($typeAnnotation = $this->annotations->get($this->target, Type::class))
		{
			return $typeAnnotation->value;
		}

		// Try determining from phpdoc (@var, @return and @param)
		if ($this->isClassProperty())
		{
			$type = $this->docReader->getPropertyClass($this->target);
			if ($type !== null)
			{
				return $type;
			}
		}

		if ($this->isGetter())
		{
			$type = $this->docReader->getMethodReturnClass($this->target);
			if ($type !== null)
			{
				return $type;
			}
		}

		if ($this->isSetter())
		{
			/** @var ReflectionParameter $setter */
			list($setter) = $this->target->getParameters();

			$type = $this->docReader->getParameterClass($setter);
			if ($type !== null)
			{
				return $type;
			}
		}

		// Fallback to parent strategy
		return parent::getType();
	}

	/**
	 * @return bool
	 */
	public function isArray()
	{
		if ($this->annotations->has($this->target, IsArray::class))
		{
			return true;
		}
		
		return parent::isArray();
	}


	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function isGetter()
	{
		if (!$this->annotations->has($this->target, Getter::class))
		{
			return parent::isGetter();
		}

		$paramCount = $this->target->getNumberOfRequiredParameters();

		if ($paramCount > 0)
		{
			throw new SerializationException("Field {$this->target->name} has {$paramCount} required parameters. Fields mapped with the @Getter annotation must have no required parameters.");
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function isSetter()
	{
		if (!$this->annotations->has($this->target, Setter::class))
		{
			return parent::isSetter();
		}

		if ($this->target->getNumberOfParameters() === 0)
		{
			// Valid setters must have at least one parameter, and at most one required parameter
			throw new SerializationException("Field {$this->target->name} has no parameters. Fields mapped with the @Setter annotation must have at least one parameter.");
		}

		$paramCount = $this->target->getNumberOfRequiredParameters();

		if ($paramCount > 1)
		{
			throw new SerializationException("Field {$this->target->name} has {$paramCount} required parameters. Fields mapped with the @Setter annotation must have one required parameter at most.");
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isIncluded()
	{
		if ($this->annotations->has($this->target, Ignore::class))
		{
			return false;
		}

		if ($this->parent->wantsExplicitInclusion() && $this->annotations->has($this->target, AbstractName::class))
		{
			return true;
		}
		
		return parent::isIncluded();
	}

	/**
	 * @return bool
	 */
	public function isReference()
	{
		if ($this->annotations->has($this->target, IsReference::class))
		{
			return true;
		}
		
		return parent::isReference();
	}

	/**
	 * @return bool
	 */
	public function isReferenceLazy()
	{
		/** @var IsReference $referenceAnnotation */
		if ($referenceAnnotation = $this->annotations->get($this->target, IsReference::class))
		{
			return $referenceAnnotation->lazy;
		}
		
		return parent::isReferenceLazy();
	}

	/**
	 * @return bool
	 */
	public function hasSerializingCustomConverter()
	{
		foreach ($this->getCustomConverterAnnotations() as $annotation)
		{
			if ($annotation->serialize)
			{
				return true;
			}
		}
		
		return parent::hasSerializingCustomConverter();
	}

	/**
	 * @return bool
	 */
	public function hasDeserializingCustomConverter()
	{
		foreach ($this->getCustomConverterAnnotations() as $annotation)
		{
			if ($annotation->deserialize)
			{
				return true;
			}
		}

		return parent::hasDeserializingCustomConverter();
	}

	/**
	 * @return string
	 */
	public function getSerializingCustomConverterType()
	{
		foreach ($this->getCustomConverterAnnotations() as $annotation)
		{
			if ($annotation->serialize)
			{
				return $annotation->value;
			}
		}

		return parent::getSerializingCustomConverterType();
	}

	/**
	 * @return string
	 */
	public function getDeserializingCustomConverterType()
	{
		foreach ($this->getCustomConverterAnnotations() as $annotation)
		{
			if ($annotation->deserialize)
			{
				return $annotation->value;
			}
		}

		return parent::getDeserializingCustomConverterType();
	}

	/**
	 * @return bool
	 */
	public function doesSerialization()
	{
		/** @var Property $annotation */
		if ($annotation = $this->annotations->get($this->target, Property::class))
		{
			return $annotation->serialize;
		}
		
		return parent::doesSerialization();
	}

	/**
	 * @return bool
	 */
	public function doesDeserialization()
	{
		/** @var Property $annotation */
		if ($annotation = $this->annotations->get($this->target, Property::class))
		{
			return $annotation->deserialize;
		}

		return parent::doesDeserialization();
	}

	/**
	 * @return CustomConverter[]
	 */
	private function getCustomConverterAnnotations()
	{
		if ($this->customConverterAnnotations === null)
		{
			$this->customConverterAnnotations = $this->annotations->get($this->target, CustomConverter::class, true);
		}

		return $this->customConverterAnnotations;
	}
}

