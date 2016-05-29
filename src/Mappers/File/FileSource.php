<?php

namespace OneOfZero\Json\Mappers\File;

use OneOfZero\Json\Mappers\AbstractArray\ArrayAbstractSource;
use RuntimeException;

abstract class FileSource extends ArrayAbstractSource
{
	/**
	 * @var string $file
	 */
	private $file;

	/**
	 * @param string $file
	 */
	public function __construct($file)
	{
		if (!file_exists($file))
		{
			throw new RuntimeException("File \"$file\" does not exist");
		}
		
		if (!is_readable($file))
		{
			throw new RuntimeException("File \"$file\" is not readable");
		}
		
		$this->file = $file;
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHash()
	{
		return sha1(__CLASS__ . $this->file);
	}
}
