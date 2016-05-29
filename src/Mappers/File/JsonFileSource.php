<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\File;

class JsonFileSource extends FileSource
{
	/**
	 * @param string $file
	 */
	public function __construct($file)
	{
		parent::__construct($file);
		
		$this->mapping = json_decode(file_get_contents($file), true);
		$this->aliases = array_key_exists('@use', $this->mapping) ? $this->mapping['@use'] : [];
	}
}
