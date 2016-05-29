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
		return $this->getChain()->isExplicitInclusionEnabled();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isMetadataDisabled()
	{
		return $this->getChain()->isMetadataDisabled();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return $this->getChain()->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		return $this->getChain()->getDeserializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return $this->getChain()->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return $this->getChain()->hasDeserializingConverter();
	}
}
