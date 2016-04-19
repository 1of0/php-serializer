<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Mappers\MapperPipeline;
use OneOfZero\Json\Mappers\ReflectionMapperFactory;
use OneOfZero\Json\Mappers\YamlMapperFactory;
use RuntimeException;

class YamlMapperTest extends AbstractMapperTest
{
	const YAML_MAPPING_FILE = __DIR__ . '/Assets/mapping.yaml';

	/**
	 * {@inheritdoc}
	 */
	protected function getPipeline()
	{
		return (new MapperPipeline)
			->addFactory(new YamlMapperFactory(self::YAML_MAPPING_FILE))
			->addFactory(new ReflectionMapperFactory())
			->build($this->defaultConfiguration)
		;
	}

	public function testInvalidMapperFile()
	{
		$this->setExpectedException(RuntimeException::class);
		new YamlMapperFactory('non-existing.yaml');
	}
}
