<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

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
		$this->aliases = array_key_exists('@use', $this->mapping) ? $this->mapping['@use'] : [];
	}
}
