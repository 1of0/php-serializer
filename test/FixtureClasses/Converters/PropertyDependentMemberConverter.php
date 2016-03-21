<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses\Converters;

use OneOfZero\Json\Nodes\MemberNode;
use OneOfZero\Json\Converters\AbstractMemberConverter;

class PropertyDependentMemberConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberNode $node)
	{
		$memberName = $node->getReflector()->name;
		
		if ($memberName === 'foo')
		{
			return 1000 - $node->getValue();
		}

		if ($memberName === 'bar')
		{
			return 1000 + $node->getValue();
		}

		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberNode $node)
	{
		$memberName = $node->getReflector()->name;
		
		if ($memberName === 'foo')
		{
			return 1000 - $node->getSerializedValue();
		}

		if ($memberName === 'bar')
		{
			return $node->getSerializedValue() - 1000;
		}

		return 0;
	}
}
