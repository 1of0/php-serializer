<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Templates;

use OneOfZero\Json\Mappers\BaseObjectMapperTrait;
use OneOfZero\Json\Mappers\ObjectMapperInterface;

class DummyObjectMapper implements ObjectMapperInterface
{
	use BaseObjectMapperTrait;
	
	/**
	 * {@inheritdoc}
	 */
	public function isExplicitInclusionEnabled()
	{
		return $this->getBase()->isExplicitInclusionEnabled();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isMetadataDisabled()
	{
		return $this->getBase()->isMetadataDisabled();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return $this->getBase()->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		return $this->getBase()->getDeserializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return $this->getBase()->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return $this->getBase()->hasDeserializingConverter();
	}
}
