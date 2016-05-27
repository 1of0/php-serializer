<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Anonymous;

use OneOfZero\Json\Mappers\BaseMapperTrait;
use OneOfZero\Json\Mappers\ObjectMapperInterface;
use stdClass;

class AnonymousObjectMapper implements ObjectMapperInterface
{
	use BaseMapperTrait;
	
	/**
	 * Holds cached field mappers for the class properties.
	 *
	 * @var AnonymousMemberMapper[]|null $members
	 */
	protected $members = null;

	/**
	 * Holds the object that is to be mapped.
	 * 
	 * @var stdClass $object
	 */
	protected $object;

	/**
	 * @param stdClass $object
	 */
	public function __construct(stdClass $object)
	{
		$this->object = $object;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getMembers()
	{
		if ($this->members !== null)
		{
			return $this->members;
		}

		$this->members = [];

		foreach (array_keys(get_object_vars($this->object)) as $memberName)
		{
			// Skip magic properties
			if (strpos($memberName, '__') === 0)
			{
				continue;
			}

			$this->members[] = new AnonymousMemberMapper($memberName);
		}

		return $this->members;
	}
	
	#region // Null getters

	/**
	 * {@inheritdoc}
	 */
	public function isExplicitInclusionEnabled()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isMetadataDisabled()
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
