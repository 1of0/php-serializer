<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\BetterAnnotations\Annotations;
use OneOfZero\Json\Annotations\Converter;
use OneOfZero\PhpDocReader\PhpDocReader;

trait AnnotationMapperTrait
{
	/**
	 * @var Annotations $annotations
	 */
	protected $annotations;

	/**
	 * @var PhpDocReader $docReader
	 */
	protected $docReader;

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
	public function hasSerializingConverter()
	{
		$annotation = $this->annotations->get($this->target, Converter::class);

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

		return $this->getBase()->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		$annotation = $this->annotations->get($this->target, Converter::class);

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

		return $this->getBase()->hasDeserializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		$annotation = $this->annotations->get($this->target, Converter::class);

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

		return $this->getBase()->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		$annotation = $this->annotations->get($this->target, Converter::class);

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

		return $this->getBase()->getDeserializingConverterType();
	}

	/**
	 * @return MapperInterface
	 */
	public abstract function getBase();
}
