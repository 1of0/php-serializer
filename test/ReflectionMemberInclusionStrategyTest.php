<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Mappers\FactoryChainFactory;
use OneOfZero\Json\Mappers\Reflection\ReflectionFactory;
use OneOfZero\Json\Serializer;

class ReflectionMemberInclusionStrategyTest extends AbstractMemberInclusionStrategyTest
{
	/**
	 * {@inheritdoc}
	 */
	protected function createSerializer($strategy)
	{
		$configuration = $this->configuration;
		$configuration->defaultMemberInclusionStrategy = $strategy;
		
		$pipeline = (new FactoryChainFactory)->withAddedFactory(new ReflectionFactory());

		return new Serializer($configuration, null, $pipeline);
	}
}
