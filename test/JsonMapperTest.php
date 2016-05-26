<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Mappers\JsonMapperFactory;
use OneOfZero\Json\Mappers\MapperPipeline;
use OneOfZero\Json\Mappers\ReflectionMapperFactory;
use RuntimeException;

class JsonMapperTest extends AbstractMapperTest
{
	const JSON_MAPPING_FILE = __DIR__ . '/Assets/mapping.json';

	/**
	 * {@inheritdoc}
	 */
	protected function getPipeline()
	{
		return (new MapperPipeline)
			->addFactory(new JsonMapperFactory(self::JSON_MAPPING_FILE))
			->addFactory(new ReflectionMapperFactory())
			->build($this->defaultConfiguration)
		;
	}

	public function testInvalidMapperFile()
	{
		$this->setExpectedException(RuntimeException::class);
		new JsonMapperFactory('non-existing.json');
	}
}