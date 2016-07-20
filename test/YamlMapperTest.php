<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Mappers\AbstractArray\ArrayFactory;
use OneOfZero\Json\Mappers\FactoryChainFactory;
use OneOfZero\Json\Mappers\File\YamlFileSource;
use OneOfZero\Json\Mappers\Reflection\ReflectionFactory;
use RuntimeException;

class YamlMapperTest extends AbstractMapperTest
{
	const YAML_MAPPING_FILE = __DIR__ . '/Assets/mapping.yaml';

	/**
	 * {@inheritdoc}
	 */
	protected function getChain()
	{
		return (new FactoryChainFactory)
			->withAddedFactory(new ArrayFactory(new YamlFileSource(self::YAML_MAPPING_FILE)))
			->withAddedFactory(new ReflectionFactory())
			->build($this->configuration)
		;
	}

	public function testInvalidMapperFile()
	{
		$this->setExpectedException(RuntimeException::class);
		new YamlFileSource('non-existing.yaml');
	}
}
