<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

/**
 * @codeCoverageIgnore Not much to test here...
 */
class NullObjectMapper implements ObjectMapperInterface
{
	use BaseObjectMapperTrait;
	
	/**
	 * {@inheritdoc}
	 */
	public function wantsExplicitInclusion()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function wantsNoMetadata()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		return null;
	}
}
