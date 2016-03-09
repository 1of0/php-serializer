<?php

namespace OneOfZero\Json\Internals\Mappers;

class YamlMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;

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
	public function getValue($instance)
	{

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

	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
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