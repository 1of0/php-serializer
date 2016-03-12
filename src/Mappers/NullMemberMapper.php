<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

class NullMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;

	/**
	 * {@inheritdoc}
	 */
	public function getValue($instance)
	{
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($instance, $value)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return null;
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
	public function isReferenceLazy()
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
