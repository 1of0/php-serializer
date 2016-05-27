<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Mappers\Null\NullMapperFactory;

class MapperPipeline
{
	/**
	 * @var MapperFactoryInterface[] $pipeline
	 */
	private $pipeline = [];

	/**
	 * @param MapperFactoryInterface $factory
	 *
	 * @return self
	 */
	public function withFactory(MapperFactoryInterface $factory)
	{
		$new = clone $this;
		$new->pipeline[] = $factory;
		return $new;
	}

	/**
	 * @param string $className
	 * 
	 * @return bool
	 */
	public function containsFactory($className)
	{
		foreach ($this->pipeline as $factory)
		{
			if (get_class($factory) === $className)
			{
				return true;
			}
		}
		
		return false;
	}

	/**
	 * @param Configuration|null $configuration
	 *
	 * @return MapperFactoryInterface
	 */
	public function build(Configuration $configuration = null)
	{
		$lastFactory = new NullMapperFactory();

		/** @var MapperFactoryInterface[] $pipeline */
		$pipeline = array_reverse($this->pipeline);

		foreach ($pipeline as $item)
		{
			$currentFactory = $item->withParent($lastFactory);

			if ($configuration !== null)
			{
				$currentFactory = $currentFactory->withConfiguration($configuration);
			}
			
			$lastFactory = $currentFactory;
		}

		return $lastFactory;
	}
}
