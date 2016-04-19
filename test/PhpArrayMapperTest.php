<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Mappers\MapperPipeline;
use OneOfZero\Json\Mappers\PhpArrayMapperFactory;
use OneOfZero\Json\Mappers\ReflectionMapperFactory;
use RuntimeException;

class PhpArrayMapperTest extends AbstractMapperTest
{
	const PHP_ARRAY_MAPPING_FILE = __DIR__ . '/Assets/mapping.php';

	/**
	 * {@inheritdoc}
	 */
	protected function getPipeline()
	{
		return (new MapperPipeline)
			->addFactory(new PhpArrayMapperFactory(self::PHP_ARRAY_MAPPING_FILE))
			->addFactory(new ReflectionMapperFactory())
			->build($this->defaultConfiguration)
		;
	}

	public function testInvalidMapperFile()
	{
		$this->setExpectedException(RuntimeException::class);
		new PhpArrayMapperFactory('non-existing.php');
	}
}
