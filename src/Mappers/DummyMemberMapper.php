<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

/**
 * @codeCoverageIgnore
 */
class DummyMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->getBase()->getName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return $this->getBase()->getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		return $this->getBase()->isIncluded();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		return $this->getBase()->isGetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		return $this->getBase()->isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		return $this->getBase()->isArray();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		return $this->getBase()->isReference();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		return $this->getBase()->isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		return $this->getBase()->isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		return $this->getBase()->isDeserializable();
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
