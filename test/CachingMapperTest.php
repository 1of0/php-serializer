<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use Doctrine\Common\Cache\ArrayCache;
use OneOfZero\Json\Mappers\AbstractArray\ArrayFactory;
use OneOfZero\Json\Mappers\FactoryChainFactory;
use OneOfZero\Json\Mappers\File\PhpFileSource;
use OneOfZero\Json\Mappers\Reflection\ReflectionFactory;

class CachingMapperTest extends AbstractMapperTest
{
	/**
	 * {@inheritdoc}
	 */
	protected function getChain()
	{
		return (new FactoryChainFactory)
			->withAddedFactory(new ArrayFactory(new PhpFileSource(PhpArrayMapperTest::PHP_ARRAY_MAPPING_FILE)))
			->withAddedFactory(new ReflectionFactory())
			->withCache(new ArrayCache())
			->build($this->configuration)
		;
	}
}
