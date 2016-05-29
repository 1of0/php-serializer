<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Null;

use OneOfZero\Json\Enums\ReferenceResolutionStrategy;
use OneOfZero\Json\Mappers\AbstractMemberMapper;

/**
 * @codeCoverageIgnore Not much to test here...
 */
class NullMemberMapper extends AbstractMemberMapper
{
	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		if ($this->getTarget() !== null)
		{
			return $this->getTarget()->name;
		}
		
		return null;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getDeserializedName()
	{
		if ($this->getTarget() !== null)
		{
			return $this->getTarget()->name;
		}
		
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		$configuration = $this->getChain()->getConfiguration();

		return $configuration->defaultReferenceResolutionStrategy == ReferenceResolutionStrategy::LAZY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
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

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		return false;
	}
}
