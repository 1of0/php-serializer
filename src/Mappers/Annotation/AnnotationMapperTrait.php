<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Annotation;

use OneOfZero\BetterAnnotations\Annotations;
use OneOfZero\Json\Annotations\Converter;
use OneOfZero\Json\Mappers\AbstractMapperChain;
use OneOfZero\Json\Mappers\MapperInterface;
use OneOfZero\Json\Mappers\MemberMapperChain;
use OneOfZero\Json\Mappers\ObjectMapperChain;

/**
 * @method AnnotationSource getSource
 * @method AbstractMapperChain|ObjectMapperChain|MemberMapperChain getChain
 */
trait AnnotationMapperTrait
{
	/**
	 * @return Annotations
	 */
	protected function getAnnotations()
	{
		return $this->getSource()->getAnnotations();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		$annotation = $this->getAnnotations()->get($this->getTarget(), Converter::class);

		if ($annotation !== null)
		{
			if ($annotation->value !== null)
			{
				return true;
			}

			if ($annotation->serializer !== null)
			{
				return true;
			}
		}

		/** @var MapperInterface $this */
		return $this->getChain()->getNext($this)->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		$annotation = $this->getAnnotations()->get($this->getTarget(), Converter::class);

		if ($annotation !== null)
		{
			if ($annotation->value !== null)
			{
				return true;
			}

			if ($annotation->deserializer !== null)
			{
				return true;
			}
		}

		/** @var MapperInterface $this */
		return $this->getChain()->getNext($this)->hasDeserializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		$annotation = $this->getAnnotations()->get($this->getTarget(), Converter::class);

		if ($annotation !== null)
		{
			if ($annotation->value !== null)
			{
				return $annotation->value;
			}

			if ($annotation->serializer !== null)
			{
				return $annotation->serializer;
			}
		}


		/** @var MapperInterface $this */
		return $this->getChain()->getNext($this)->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		$annotation = $this->getAnnotations()->get($this->getTarget(), Converter::class);

		if ($annotation !== null)
		{
			if ($annotation->value !== null)
			{
				return $annotation->value;
			}

			if ($annotation->deserializer !== null)
			{
				return $annotation->deserializer;
			}
		}

		/** @var MapperInterface $this */
		return $this->getChain()->getNext($this)->getDeserializingConverterType();
	}
}
