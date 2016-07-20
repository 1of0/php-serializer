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
use OneOfZero\Json\Mappers\Caching\CacheFactory;
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
	public function withAddedFactory(FactoryInterface $factory)
	{
		$new = clone $this;
		
		if ($factory instanceof CacheFactory)
		{
			throw new RuntimeException('Caching must be enabled with the withCache() method');
		}

		$new->chain[] = $factory;
		
		return $new;
	}

	/**
	 * @param CacheProvider $cache
	 *
	 * @return self
	 */
	public function withCache(CacheProvider $cache = null)
	{
		$new = clone $this;
		$new->cache = $cache;
		return $new;
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

		$cacheFactory = null;
		
		if ($this->cache !== null)
		{
			$cacheFactory = new CacheFactory(new CacheSource($this->cache));
		}
		
		return new FactoryChain(array_reverse($this->chain), $configuration, $cacheFactory);
	}
}
