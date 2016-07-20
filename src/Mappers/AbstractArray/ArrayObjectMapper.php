<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\AbstractArray;

use OneOfZero\Json\Mappers\AbstractObjectMapper;

class ArrayObjectMapper extends AbstractObjectMapper
{
	use ArrayMapperTrait;

	public static $METADATA_ATTR = 'metadata';
	public static $EXPLICIT_INCLUSION_ATTR = 'explicit';
	
	/**
	 * {@inheritdoc}
	 */
	public function isExplicitInclusionEnabled()
	{
		if ($this->hasAttribute(self::$EXPLICIT_INCLUSION_ATTR))
		{
			return (bool)$this->readAttribute(self::$EXPLICIT_INCLUSION_ATTR);
		}
		
		return parent::isExplicitInclusionEnabled();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isMetadataDisabled()
	{
		if ($this->hasAttribute(self::$METADATA_ATTR))
		{
			return ((bool)$this->readAttribute(self::$METADATA_ATTR)) === false;
		}
		
		return parent::isMetadataDisabled();
	}
}
