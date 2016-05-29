<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Anonymous;

use OneOfZero\Json\Mappers\AbstractObjectMapper;
use stdClass;

class AnonymousObjectMapper extends AbstractObjectMapper
{
	/**
	 * @var stdClass $object
	 */
	protected $object;

	/**
	 * @param stdClass $object
	 */
	public function __construct(stdClass $object)
	{
		parent::__construct();
		
		$this->object = $object;
		$this->setChain(new AnonymousMapperChain($this));
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapMembers()
	{
		$members = [];

		foreach (array_keys(get_object_vars($this->object)) as $memberName)
		{
			// Skip magic properties
			if (strpos($memberName, '__') === 0)
			{
				continue;
			}

			$mapper = new AnonymousMemberMapper($memberName);
			$chain = new AnonymousMapperChain($mapper);
			$mapper->setChain($chain);
			
			$members[] = $chain;
		}

		return $members;
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
