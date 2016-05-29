<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Templates;

use OneOfZero\Json\Mappers\BaseMemberMapperTrait;
use OneOfZero\Json\Mappers\MemberMapperInterface;

class DummyMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;

	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		return $this->getChain()->getSerializedName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return $this->getChain()->getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		return $this->getChain()->isIncluded();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		return $this->getChain()->isGetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		return $this->getChain()->isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		return $this->getChain()->isArray();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		return $this->getChain()->isReference();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		return $this->getChain()->isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		return $this->getChain()->isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		return $this->getChain()->isDeserializable();
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
