<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

class AnonymousMemberMapper implements MemberMapperInterface
{
	use BaseMapperTrait;

	/**
	 * Holds the member parent.
	 *
	 * @var ObjectMapperInterface $memberParent
	 */
	protected $memberParent;

	/**
	 * Holds the member's name.
	 * 
	 * @var string $name
	 */
	protected $name;

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setMemberParent(ObjectMapperInterface $parent)
	{
		$this->memberParent = $parent;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue($instance)
	{
		return $instance->{$this->name};
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($instance, $value)
	{
		$instance->{$this->name} = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	#region // Null getters

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
	public function isIncluded()
	{
		return true;
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
	
	#endregion
}
