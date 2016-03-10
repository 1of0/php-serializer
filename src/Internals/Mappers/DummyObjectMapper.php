<?php

namespace OneOfZero\Json\Internals\Mappers;

class DummyObjectMapper implements ObjectMapperInterface
{
	use BaseObjectMapperTrait;

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
	public function wantsExplicitInclusion()
	{
		return $this->base->wantsExplicitInclusion();
	}

	/**
	 * {@inheritdoc}
	 */
	public function wantsNoMetadata()
	{
		return $this->base->wantsNoMetadata();
	}
}