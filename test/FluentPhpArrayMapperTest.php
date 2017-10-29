<?php
/**
 * Copyright (c) 2017 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Mappers\AbstractArray\ArrayFactory;
use OneOfZero\Json\Mappers\FactoryChainFactory;
use OneOfZero\Json\Mappers\File\FluentPhpFileSource;
use OneOfZero\Json\Mappers\Reflection\ReflectionFactory;
use RuntimeException;

class FluentPhpArrayMapperTest extends AbstractMapperTest
{
	const PHP_ARRAY_MAPPING_FILE = __DIR__ . '/Assets/mapping.fluent.php';

	/**
	 * {@inheritdoc}
	 */
	protected function getChain()
	{
		return (new FactoryChainFactory)
			->withAddedFactory(new ArrayFactory(new FluentPhpFileSource(self::PHP_ARRAY_MAPPING_FILE)))
			->withAddedFactory(new ReflectionFactory())
			->build($this->configuration)
		;
	}

	public function testInvalidMapperFile()
	{
		$this->setExpectedException(RuntimeException::class);
		new FluentPhpFileSource('non-existing.php');
	}
}
