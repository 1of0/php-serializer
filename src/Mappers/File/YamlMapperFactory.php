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
use Symfony\Component\Yaml\Parser;

class YamlMapperFactory extends ArrayMapperFactory
{
	/**
	 * @param string $mappingFile
	 */
	public function __construct($mappingFile)
	{
		if (!class_exists(Parser::class))
		{
			// @codeCoverageIgnoreStart
			throw new RuntimeException('The package symfony/yaml is required to be able to use the yaml mapper');
			// @codeCoverageIgnoreEnd
		}

		if (!file_exists($mappingFile))
		{
			throw new RuntimeException("File \"$mappingFile\" does not exist");
		}

		$parser = new Parser();
		
		$this->mapping = $parser->parse(file_get_contents($mappingFile));
		$this->aliases = array_key_exists('@use', $this->mapping) ? $this->mapping['@use'] : [];
	}
}
