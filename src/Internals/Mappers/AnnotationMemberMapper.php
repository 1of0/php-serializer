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

class AnnotationMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;
	
	/**
	 * @var Annotations $annotations
	 */
	private $annotations;

	/**
	 * @var PhpDocReader $docReader
	 */
	private $docReader;

	/**
	 * @var Converter[]|null $converterAnnotations
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
	public function getValue($instance)
	{
		return $this->getBase()->getValue($instance);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($instance, $value)
	{
		$this->getBase()->setValue($instance, $value);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		/** @var AbstractName $nameAnnotation */
		$nameAnnotation = $this->annotations->get($this->target, AbstractName::class);

		if ($nameAnnotation && $nameAnnotation->name !== null)
		{
			return $nameAnnotation->name;
		}
		
		return $this->getBase()->getName();
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
		return $this->getBase()->getType();
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
		
		return $this->getBase()->isArray();
	}

	/**
	 * {@inheritdoc}
	 * 
	 * @throws SerializationException
	 */
	public function isGetter()
	{
		if ($this->annotations->has($this->target, Getter::class))
		{
			$this->validateGetterSignature();
			return true;
		}

		return $this->getBase()->isGetter();
	}

	/**
	 * {@inheritdoc}
	 * 
	 * @throws SerializationException
	 */
	public function isSetter()
	{
		if ($this->annotations->has($this->target, Setter::class))
		{
			$this->validateSetterSignature();
			return true;
		}

		return $this->getBase()->isSetter();
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

		if ($this->annotations->has($this->target, AbstractName::class))
		{
			return true;
		}

		if ($this->memberParent->wantsExplicitInclusion()/* && !$this->annotations->has($this->target, AbstractName::class) */)
		{
			return false;
		}
		
		return $this->getBase()->isIncluded();
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
		
		return $this->getBase()->isReference();
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
		
		return $this->getBase()->isReferenceLazy();
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
		
		return $this->getBase()->hasSerializingConverter();
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

		return $this->getBase()->hasDeserializingConverter();
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

		return $this->getBase()->getSerializingConverterType();
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

		return $this->getBase()->getDeserializingConverterType();
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
		
		return $this->getBase()->isSerializable();
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

		return $this->getBase()->isDeserializable();
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

