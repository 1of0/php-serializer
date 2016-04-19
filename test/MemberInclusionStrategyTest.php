<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Serializer;

class MemberInclusionStrategyTest extends AbstractMemberInclusionStrategyTest
{
	/**
	 * {@inheritdoc}
	 */
	protected function createSerializer($strategy)
	{
		$configuration = $this->defaultConfiguration;
		$configuration->defaultMemberInclusionStrategy = $strategy;

		return new Serializer($configuration);
	}
}
