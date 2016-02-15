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

class ObjectAnnotationMapper extends AbstractObjectMapper
{
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
		return $this->annotations->has($this->target, ExplicitInclusion::class);
	}

	public function wantsNoMetadata()
	{
		return $this->annotations->has($this->target, NoMetadata::class);
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
	 * @return AbstractMemberMapper
	 */
	protected function getMemberMapper()
	{
		return new MemberAnnotationMapper($this->annotations);
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
