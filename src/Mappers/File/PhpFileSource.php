<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\File;

class PhpFileSource extends FileSource
{
	/**
	 * {@inheritdoc}
	 */
	protected function load()
	{
		/** @noinspection PhpIncludeInspection */
		$this->mapping = include($this->getFile());
	}
}
