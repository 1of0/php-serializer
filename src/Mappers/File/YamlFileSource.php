<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\File;

use RuntimeException;
use Symfony\Component\Yaml\Parser;

class YamlFileSource extends FileSource
{
	/**
	 * @param string $file
	 */
	public function __construct($file)
	{
		parent::__construct($file);

		if (!class_exists(Parser::class))
		{
			// @codeCoverageIgnoreStart
			throw new RuntimeException('The package symfony/yaml is required to be able to use the yaml mapper');
			// @codeCoverageIgnoreEnd
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function load()
	{
		$parser = new Parser();
		
		$this->mapping = $parser->parse(file_get_contents($this->getFile()));
		$this->aliases = array_key_exists('@use', $this->mapping) ? $this->mapping['@use'] : [];
	}
}
