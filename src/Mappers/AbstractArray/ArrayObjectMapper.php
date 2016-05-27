<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\AbstractArray;

use OneOfZero\Json\Mappers\BaseObjectMapperTrait;
use OneOfZero\Json\Mappers\ObjectMapperInterface;

class ArrayObjectMapper extends ArrayAbstractMapper implements ObjectMapperInterface
{
	use BaseObjectMapperTrait;

	const METADATA_ATTR = 'metadata';
	const EXPLICIT_INCLUSION_ATTR = 'explicit';
	
	/**
	 * {@inheritdoc}
	 */
	public function isExplicitInclusionEnabled()
	{
		if ($this->hasAttribute(self::EXPLICIT_INCLUSION_ATTR))
		{
			return (bool)$this->readAttribute(self::EXPLICIT_INCLUSION_ATTR);
		}
		
		return $this->getBase()->isExplicitInclusionEnabled();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isMetadataDisabled()
	{
		if ($this->hasAttribute(self::METADATA_ATTR))
		{
			return ((bool)$this->readAttribute(self::METADATA_ATTR)) === false;
		}
		
		return $this->getBase()->isMetadataDisabled();
	}
}
