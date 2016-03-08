<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\AbstractMemberConverter;
use OneOfZero\Json\Internals\Contexts\MemberContext;

class ClassDependentMemberConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberContext $context)
	{
		$object = $context->getValue();
		
		if ($object instanceof SimpleClass)
		{
			return implode('|', [$object->foo, $object->bar]);
		}

		if ($object instanceof ReferableClass)
		{
			return $object->getId();
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberContext $context)
	{
		$class = $context->getMapper()->getType();
		
		if ($class === SimpleClass::class)
		{
			list($foo, $bar) = explode('|', $context->getSerializedValue());
			return new SimpleClass($foo, $bar);
		}

		if ($class === ReferableClass::class)
		{
			return new ReferableClass($context->getSerializedValue());
		}

		return null;
	}
}
