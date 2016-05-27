<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Mappers\File\JsonMapperFactory;
use OneOfZero\Json\Mappers\MapperPipeline;
use OneOfZero\Json\Mappers\Reflection\ReflectionMapperFactory;
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
			->withFactory(new JsonMapperFactory(self::JSON_MAPPING_FILE))
			->withFactory(new ReflectionMapperFactory())
			->build($this->defaultConfiguration)
		;
	}

	public function testInvalidMapperFile()
	{
		$this->setExpectedException(RuntimeException::class);
		new JsonMapperFactory('non-existing.json');
	}
}
