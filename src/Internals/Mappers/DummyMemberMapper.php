<?php

namespace OneOfZero\Json\Internals\Mappers;

class DummyMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return $this->base->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return $this->base->hasDeserializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return $this->base->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		return $this->base->getDeserializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue($instance)
	{
		return $this->base->getValue($instance);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($instance, $value)
	{
		$this->base->setValue($instance, $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->base->getName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return $this->base->getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		return $this->base->isArray();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		return $this->base->isGetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		return $this->base->isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		return $this->base->isReference();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		return $this->base->isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		return $this->base->isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		return $this->base->isDeserializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		return $this->base->isIncluded();
	}
}