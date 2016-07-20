<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Caching;

use OneOfZero\Json\Mappers\AbstractMapperChain;
use OneOfZero\Json\Mappers\AbstractObjectMapper;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

class CachedObjectMapper extends AbstractObjectMapper
{
	/**
	 * @var array $mapping
	 */
	private $mapping;

	/**
	 * @param array $mapping
	 * @param Reflector|ReflectionClass|ReflectionProperty|ReflectionMethod $target
	 * @param AbstractMapperChain|null $chain
	 */
	public function __construct(
		array $mapping,
		Reflector $target = null,
		AbstractMapperChain $chain = null
	)
	{
		parent::__construct(null, $target, $chain);
		
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
