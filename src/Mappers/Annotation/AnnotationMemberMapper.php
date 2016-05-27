<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Annotation;

use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\AbstractName;
use OneOfZero\Json\Annotations\Getter;
use OneOfZero\Json\Annotations\Ignore;
use OneOfZero\Json\Annotations\IsArray;
use OneOfZero\Json\Annotations\IsReference;
use OneOfZero\Json\Annotations\Property;
use OneOfZero\Json\Annotations\Setter;
use OneOfZero\Json\Annotations\Type;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Mappers\BaseMemberMapperTrait;
use OneOfZero\Json\Mappers\MemberMapperInterface;
use ReflectionParameter;

class AnnotationMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;
	use AnnotationMapperTrait;
		
	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		/** @var AbstractName $nameAnnotation */
		$nameAnnotation = $this->annotations->get($this->target, AbstractName::class);

		if ($nameAnnotation && $nameAnnotation->name !== null)
		{
			return $nameAnnotation->name;
		}
		
		return $this->getBase()->getSerializedName();
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

		if ($this->isClassMethod())
		{
			if ($this->isGetter() || $this->isSetter())
			{
				return true;
			}
		}

		if ($this->annotations->has($this->target, AbstractName::class))
		{
			return true;
		}

		if ($this->memberParent->isExplicitInclusionEnabled()/* && !$this->annotations->has($this->target, AbstractName::class) */)
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
	public function isSerializable()
	{
		if ($this->isClassProperty())
		{
			/** @var Property $annotation */
			if ($annotation = $this->annotations->get($this->target, Property::class))
			{
				return $annotation->serialize;
			}
		}

		if ($this->isClassMethod() && $this->isGetter())
		{
			return true;
		}
		
		return $this->getBase()->isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		if ($this->isClassProperty())
		{
			/** @var Property $annotation */
			if ($annotation = $this->annotations->get($this->target, Property::class))
			{
				return $annotation->deserialize;
			}
		}

		if ($this->isClassMethod() && $this->isSetter())
		{
			return true;
		}

		return $this->getBase()->isDeserializable();
	}
}

