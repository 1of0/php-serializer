<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use ReflectionMethod;
use ReflectionProperty;

trait BaseObjectMapperTrait
{
	use BaseMapperTrait;
	
	/**
	 * Holds cached field mappers for the class properties.
	 *
	 * @var ReflectionMemberMapper[]|null $members
	 */
	protected $members = null;

	/**
	 * {@inheritdoc}
	 */
	public final function getMembers()
	{
		if ($this->members === null)
		{
			$this->members = array_merge(
				$this->mapMembers($this->target->getProperties()),
				$this->mapMembers($this->target->getMethods(), true)
			);
		}
		return $this->members;
	}

	/**
	 * Creates, provisions, and returns field mappers for the provided reflection objects.
	 *
	 * The filterMagic parameter can be used to filter out magic methods and properties.
	 *
	 * @param ReflectionProperty[]|ReflectionMethod[] $fields
	 * @param bool $filterMagic
	 *
	 * @return MemberMapperInterface[]
	 */
	protected function mapMembers($fields, $filterMagic = false)
	{
		/** @var ObjectMapperInterface $this */
		
		$fieldMappings = [];

		foreach ($fields as $field)
		{
			// Skip magic properties/methods
			if ($filterMagic && strpos($field->name, '__') === 0)
			{
				continue;
			}

			$fieldMappings[] = $this->getFactory()->mapMember($field, $this);
		}

		return $fieldMappings;
	}
}
