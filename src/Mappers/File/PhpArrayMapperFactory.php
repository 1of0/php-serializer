<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\File;

use OneOfZero\Json\Mappers\AbstractArray\ArrayMapperFactory;
use RuntimeException;

class PhpArrayMapperFactory extends ArrayMapperFactory
{
	/**
	 * @param string $mappingFile
	 */
	public function __construct($mappingFile)
	{
		if (!file_exists($mappingFile))
		{
			throw new RuntimeException("File \"$mappingFile\" does not exist");
		}

		/** @noinspection PhpIncludeInspection */
		$this->mapping = include($mappingFile);
	}
}
