<?php
/**
 * Copyright (c) 2017 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\File;

use OneOfZero\Json\Fluent\Mapping;

class FluentPhpFileSource extends FileSource
{
	/**
	 * {@inheritdoc}
	 */
	protected function load()
	{
		/** @noinspection PhpIncludeInspection */
		$mapping = include($this->getFile());

		if ($mapping instanceof Mapping)
		{
			$mapping = $mapping->__toArray();
		}

		$this->mapping = $mapping;
	}
}
