<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Annotation;

use OneOfZero\Json\Annotations\AbstractName;
use OneOfZero\Json\Annotations\Getter;
use OneOfZero\Json\Annotations\Ignore;
use OneOfZero\Json\Annotations\IsArray;
use OneOfZero\Json\Annotations\IsReference;
use OneOfZero\Json\Annotations\Property;
use OneOfZero\Json\Annotations\Setter;
use OneOfZero\Json\Annotations\Type;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Mappers\AbstractMemberMapper;

class AnnotationMemberMapper extends AbstractMemberMapper
{
	use AnnotationMapperTrait;
		
	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		/** @var AbstractName $nameAnnotation */
		$nameAnnotation = $this->getAnnotations()->get($this->getTarget(), AbstractName::class);

		if ($nameAnnotation && $nameAnnotation->name !== null)
		{
			return $nameAnnotation->name;
		}
		
		return parent::getSerializedName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		if ($typeAnnotation = $this->getAnnotations()->get($this->getTarget(), Type::class))
		{
			return $typeAnnotation->value;
		}

		return parent::getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		if ($this->getAnnotations()->has($this->getTarget(), IsArray::class))
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
		if ($this->getAnnotations()->has($this->getTarget(), Getter::class))
		{
			$this->validateGetterSignature();
			return true;
		}

		return parent::isGetter();
	}

	/**
	 * {@inheritdoc}
	 * 
	 * @throws SerializationException
	 */
	public function isSetter()
	{
		if ($this->getAnnotations()->has($this->getTarget(), Setter::class))
		{
			$this->validateSetterSignature();
			return true;
		}

		return parent::isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		if ($this->getAnnotations()->has($this->getTarget(), Ignore::class))
		{
			return false;
		}

		if ($this->isClassMethod())
		{
			if ($this->getChain()->getTop()->isGetter() || $this->getChain()->getTop()->isSetter())
			{
				return true;
			}
		}

		if ($this->getAnnotations()->has($this->getTarget(), AbstractName::class))
		{
			return true;
		}

		if ($this->getChain()->getParent()->getTop()->isExplicitInclusionEnabled())
		{
			return false;
		}
		
		return parent::isIncluded();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		if ($this->getAnnotations()->has($this->getTarget(), IsReference::class))
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
		if ($referenceAnnotation = $this->getAnnotations()->get($this->getTarget(), IsReference::class))
		{
			return $referenceAnnotation->lazy;
		}
		
		return parent::isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		if ($this->isClassProperty())
		{
			/** @var Property $annotation */
			if ($annotation = $this->getAnnotations()->get($this->getTarget(), Property::class))
			{
				return $annotation->serialize;
			}
		}

		if ($this->isClassMethod() && $this->getChain()->getTop()->isGetter())
		{
			return true;
		}
		
		return parent::isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		if ($this->isClassProperty())
		{
			/** @var Property $annotation */
			if ($annotation = $this->getAnnotations()->get($this->getTarget(), Property::class))
			{
				return $annotation->deserialize;
			}
		}

		if ($this->isClassMethod() && $this->getChain()->getTop()->isSetter())
		{
			return true;
		}

		return parent::isDeserializable();
	}
}

