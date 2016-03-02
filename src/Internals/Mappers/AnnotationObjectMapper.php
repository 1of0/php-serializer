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
use OneOfZero\Json\Annotations\Converter;
use OneOfZero\Json\Annotations\ExplicitInclusion;
use OneOfZero\Json\Annotations\NoMetadata;

/**
 * Implementation of a mapper that maps the serialization metadata for a class using annotations.
 */
class AnnotationObjectMapper implements ObjectMapperInterface
{
	use BaseObjectMapperTrait;
	
	/**
	 * @var Annotations $annotations
	 */
	private $annotations;

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
	}

	public function wantsExplicitInclusion()
	{
		if ($this->annotations->has($this->target, ExplicitInclusion::class))
		{
			return true;
		}
		
		return $this->getBase()->wantsExplicitInclusion();
	}

	public function wantsNoMetadata()
	{
		if ($this->annotations->has($this->target, NoMetadata::class))
		{
			return true;
		}
		
		return $this->getBase()->wantsNoMetadata();
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
