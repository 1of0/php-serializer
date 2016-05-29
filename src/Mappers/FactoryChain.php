<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Mappers\Caching\CachingMapperFactory;
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
	 * @param FactoryInterface[] $chain
	 * @param Configuration $configuration
	 */
	public function __construct(array $chain, Configuration $configuration)
	{
		foreach ($chain as $factory)
		{
			$this->chain[] = clone $factory;
		}
		
		$this->configuration = $configuration;
	}

	/**
	 * @param ReflectionClass $target
	 * 
	 * @return ObjectMapperInterface
	 */
	public function mapObject(ReflectionClass $target)
	{
		// TODO: Work in cache here
		
		$chain = new ObjectMapperChain($target, $this);
		
		return $chain->getTop();
	}

	/**
	 * @return string
	 */
	public function getHash()
	{
		$pipelineHash = '';
		
		foreach ($this->chain as $factory)
		{
			$pipelineHash = sha1($pipelineHash . get_class($factory));
		}
		
		return sha1($this->configuration->getHash() . $pipelineHash);
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
	 * @param bool $noCache
	 * 
	 * @return int
	 */
	public function getChainLength($noCache = false)
	{
		$chainLength = count($this->chain);
				
		if ($noCache && $this->chain[$chainLength - 1] instanceof CachingMapperFactory)
		{
			return $chainLength - 1;
		}
		
		return $chainLength;
	}
}
