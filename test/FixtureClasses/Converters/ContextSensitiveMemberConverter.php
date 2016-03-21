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
	public function serialize(MemberNode $context)
	{
		/** @var ClassUsingConverters $parentInstance */
		$parentInstance = $context->getParent()->getInstance();

		return intval($context->getValue()) * intval($parentInstance->referableClass->getId());
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberNode $context)
	{
		/** @var ClassUsingConverters $deserializedParent */
		$deserializedParent = $context->getParent()->getInstance();

		return intval($context->getSerializedValue()) / intval($deserializedParent->referableClass->getId());
	}
}
