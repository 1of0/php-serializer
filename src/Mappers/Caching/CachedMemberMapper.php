<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Caching;

use OneOfZero\Json\Mappers\AbstractMapperChain;
use OneOfZero\Json\Mappers\AbstractMemberMapper;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

class CachedMemberMapper extends AbstractMemberMapper
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
	public function getDeserializedName()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		return $this->mapping[__FUNCTION__];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		return $this->mapping[__FUNCTION__];
	}
}
