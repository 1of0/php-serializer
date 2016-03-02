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

class ContextSensitiveAbstractConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberContext $context)
	{
		/** @var ClassUsingCustomConverters $parentInstance */
		$parentInstance = $context->getParent()->getInstance();

		return intval($context->getValue()) * intval($parentInstance->referableClass->getId());
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberContext $context)
	{
		/** @var ClassUsingCustomConverters $deserializedParent */
		$deserializedParent = $context->getParent()->getSerializedInstance();

		return intval($context->getSerializedValue()) / intval($deserializedParent->referableClass->getId());
	}
}
