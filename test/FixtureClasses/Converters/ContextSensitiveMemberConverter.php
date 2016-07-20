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
use OneOfZero\Json\Test\FixtureClasses\ClassUsingConverters;

class ContextSensitiveMemberConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberNode $node, $typeHint = null)
	{
		/** @var ClassUsingConverters $parentInstance */
		$parentInstance = $node->getParent()->getInstance();

		return intval($node->getValue()) * intval($parentInstance->referableClass->getId());
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberNode $node, $typeHint = null)
	{
		/** @var ClassUsingConverters $deserializedParent */
		$deserializedParent = $node->getParent()->getInstance();

		return intval($node->getSerializedValue()) / intval($deserializedParent->referableClass->getId());
	}
}
