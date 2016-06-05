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
use OneOfZero\Json\Mappers\File\XmlFileSource;
use OneOfZero\Json\Mappers\Reflection\ReflectionFactory;
use RuntimeException;

class XmlMapperTest extends AbstractMapperTest
{
	const XML_MAPPING_FILE = __DIR__ . '/Assets/mapping.xml';

	/**
	 * {@inheritdoc}
	 */
	protected function getChain()
	{
		return (new FactoryChainFactory)
			->withAddedFactory(new ArrayFactory(new XmlFileSource(self::XML_MAPPING_FILE)))
			->withAddedFactory(new ReflectionFactory())
			->build($this->defaultConfiguration)
		;
	}

	public function testInvalidMapperFile()
	{
		$this->setExpectedException(RuntimeException::class);
		new XmlFileSource('non-existing.xml');
	}
}
