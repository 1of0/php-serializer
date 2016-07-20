<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Converters\MemberConverterInterface;
use OneOfZero\Json\Nodes\MemberNode;

class StaticCustomConverter implements MemberConverterInterface 
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberNode $node, $typeHint = null)
	{
		return 'foo';
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberNode $node, $typeHint = null)
	{
		return 'bar';
	}
}
