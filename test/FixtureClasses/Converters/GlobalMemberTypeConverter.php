<?php

namespace OneOfZero\Json\Test\FixtureClasses\Converters;

use OneOfZero\Json\Converters\AbstractMemberConverter;
use OneOfZero\Json\Nodes\MemberNode;

class GlobalMemberTypeConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberNode $node, $typeHint = null)
	{
		return base64_encode(serialize($node->getValue()));
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberNode $node, $typeHint = null)
	{
		return unserialize(base64_decode($node->getSerializedValue()));
	}
}
