<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Mappers\Caching\CacheFactory;
use ReflectionClass;

class FactoryChain
{
	/**
	 * @var FactoryInterface[] $chain
	 */
	private $chain;

	/**
	 * @var Configuration $configuration
	 */
	private $configuration;

	/**
	 * @var CacheFactory $cacheFactory
	 */
	private $cacheFactory;

	/**
	 * @param FactoryInterface[] $chain
	 * @param Configuration $configuration
	 * @param CacheFactory $cacheFactory
	 */
	public function __construct(array $chain, Configuration $configuration, CacheFactory $cacheFactory = null)
	{
		$this->chain = [];
		
		foreach ($chain as $factory)
		{
			$this->chain[] = clone $factory;
		}
		
		$this->configuration = $configuration;
		$this->cacheFactory = $cacheFactory;
	}

	/**
	 * @param ReflectionClass $target
	 * 
	 * @return ObjectMapperInterface
	 */
	public function mapObject(ReflectionClass $target)
	{
		$chain = new ObjectMapperChain($target, $this);
		
		return $chain->getTop(false);
	}

	/**
	 * @return string
	 */
	public function getHash()
	{
		$chainHash = '';
		
		foreach ($this->chain as $factory)
		{
			$chainHash = sha1($chainHash . get_class($factory));
			
			if ($factory->getSource() !== null)
			{
				$chainHash = sha1($chainHash . $factory->getSource()->getHash());
			}
		}
		
		return sha1($this->configuration->getHash() . $chainHash);
	}

	/**
	 * @return Configuration
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * @param int $index
	 * 
	 * @return FactoryInterface
	 */
	public function getFactory($index)
	{
		return $this->chain[$index];
	}

	/**
	 * @return CacheFactory
	 */
	public function getCacheFactory()
	{
		return $this->cacheFactory;
	}

	/**
	 * @return int
	 */
	public function getChainLength()
	{		
		return count($this->chain);
	}
}
