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

	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function wantsExplicitInclusion()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function wantsNoMetadata()
	{

	}

	/**
	 * @return array
	 */
	public function getMapping()
	{
		return $this->mapping;
	}
}