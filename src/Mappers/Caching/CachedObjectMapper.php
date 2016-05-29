<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Caching;

use OneOfZero\Json\Mappers\AbstractObjectMapper;
use ReflectionMethod;
use ReflectionProperty;

class CachedObjectMapper extends AbstractObjectMapper
{
	/**
	 * @var array $mapping
	 */
	private $mapping;

	/**
	 * @param array $mapping
	 */
	public function __construct(array $mapping)
	{
		parent::__construct();
		
		$this->mapping = $mapping;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isExplicitInclusionEnabled()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isMetadataDisabled()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMembers()
	{
		$members = [];

		foreach ($this->mapping['__members'] as $mapping)
		{
			$members[] = new CachedMemberMapper($mapping);
		}

		return $members;
	}
}
