<?php

namespace OneOfZero\Json\Internals\Mappers;

class YamlObjectMapper implements ObjectMapperInterface
{
	use BaseObjectMapperTrait;

	/**
	 * @var array $mapping
	 */
	private $mapping;

	/**
	 * @param array $mapping
	 */
	public function __construct(array $mapping)
	{
		$this->mapping = $mapping;
	}

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

	/**
	 * @return array
	 */
	public function getMapping()
	{
		return $this->mapping;
	}
}