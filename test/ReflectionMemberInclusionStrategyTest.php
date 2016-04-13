<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Mappers\MapperPipeline;
use OneOfZero\Json\Mappers\ReflectionMapperFactory;
use OneOfZero\Json\Serializer;

class ReflectionMemberInclusionStrategyTest extends AbstractMemberInclusionStrategyTest
{
	/**
	 * {@inheritdoc}
	 */
	protected function createSerializer($strategy)
	{
		$configuration = new Configuration();
		$configuration->defaultMemberInclusionStrategy = $strategy;
		
		$pipeline = (new MapperPipeline)
			->addFactory(new ReflectionMapperFactory())
			->build($configuration);

		return new Serializer($configuration, null, $pipeline);
	}
}