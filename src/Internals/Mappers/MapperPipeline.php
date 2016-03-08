<?php

namespace OneOfZero\Json\Internals\Mappers;

use OneOfZero\Json\Configuration;

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
	public function addFactory(MapperFactoryInterface $factory)
	{
		$this->pipeline[] = $factory;
		return $this;
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