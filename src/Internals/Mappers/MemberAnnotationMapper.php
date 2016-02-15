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
use OneOfZero\Json\Annotations\Converter;
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

class MemberAnnotationMapper extends AbstractMemberMapper
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
	 * @var Converter[] $converterAnnotations
	 */
	private $converterAnnotations = null;

	/**
	 * @param Annotations $annotations
	 */
	public function __construct(Annotations $annotations)
	{
		$this->annotations = $annotations;
		$this->docReader = new PhpDocReader(true);
	}

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
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
	 * {@inheritdoc}
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
	 * {@inheritdoc}
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
	 * {@inheritdoc}
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
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		foreach ($this->getConverterAnnotations() as $annotation)
		{
			if ($annotation->serialize)
			{
				return true;
			}
		}
		
		return parent::hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		foreach ($this->getConverterAnnotations() as $annotation)
		{
			if ($annotation->deserialize)
			{
				return true;
			}
		}

		return parent::hasDeserializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		foreach ($this->getConverterAnnotations() as $annotation)
		{
			if ($annotation->serialize)
			{
				return $annotation->value;
			}
		}

		return parent::getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		foreach ($this->getConverterAnnotations() as $annotation)
		{
			if ($annotation->deserialize)
			{
				return $annotation->value;
			}
		}

		return parent::getDeserializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		/** @var Property $annotation */
		if ($annotation = $this->annotations->get($this->target, Property::class))
		{
			return $annotation->serialize;
		}
		
		return parent::isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		/** @var Property $annotation */
		if ($annotation = $this->annotations->get($this->target, Property::class))
		{
			return $annotation->deserialize;
		}

		return parent::isDeserializable();
	}

	/**
	 * @return Converter[]
	 */
	private function getConverterAnnotations()
	{
		if ($this->converterAnnotations === null)
		{
			$this->converterAnnotations = $this->annotations->get($this->target, Converter::class, true);
		}

		return $this->converterAnnotations;
	}
}

