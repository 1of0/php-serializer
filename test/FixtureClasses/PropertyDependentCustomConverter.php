<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\AbstractMemberConverter;
use OneOfZero\Json\Internals\MemberContext;

class PropertyDependentAbstractConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberContext $context)
	{
		$memberName = $context->getReflector()->name;
		
		if ($memberName === 'foo')
		{
			return 1000 - $context->getValue();
		}

		if ($memberName === 'bar')
		{
			return 1000 + $context->getValue();
		}

		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberContext $context)
	{
		$memberName = $context->getReflector()->name;
		
		if ($memberName === 'foo')
		{
			return 1000 - $context->getSerializedValue();
		}

		if ($memberName === 'bar')
		{
			return $context->getSerializedValue() - 1000;
		}

		return 0;
	}
}
