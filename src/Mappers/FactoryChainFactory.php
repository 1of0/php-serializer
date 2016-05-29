<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use Doctrine\Common\Cache\CacheProvider;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Mappers\Caching\CacheSource;
use OneOfZero\Json\Mappers\Caching\CachingMapperFactory;
use RuntimeException;

class FactoryChainFactory
{
	/**
	 * @var FactoryInterface[] $chain
	 */
	private $chain = [];

	/**
	 * @var CacheProvider $cache
	 */
	private $cache;

	/**
	 * @param FactoryInterface $factory
	 * 
	 * @return self
	 */
	public function addFactory(FactoryInterface $factory)
	{
		if ($factory instanceof CachingMapperFactory)
		{
			throw new RuntimeException('Caching must be enabled with the withCache() method');
		}

		$this->chain[] = $factory;
		
		return $this;
	}

	/**
	 * @param CacheProvider $cache
	 *
	 * @return self
	 */
	public function setCache(CacheProvider $cache = null)
	{
		$this->cache = $cache;
		
		return $this;
	}

	/**
	 * @param Configuration $configuration
	 *
	 * @return FactoryChain
	 */
	public function build(Configuration $configuration)
	{
		if (count($this->chain) === 0)
		{
			throw new RuntimeException('There are no mapper factories in the chain');
		}

		$chain = array_reverse($this->chain);
		
		if ($this->cache !== null)
		{
			$chain[] = new CachingMapperFactory(new CacheSource($this->cache));
		}
		
		return new FactoryChain($chain, $configuration);
	}
}
