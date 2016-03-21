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
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;

class ClassDependentMemberConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberNode $context)
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
	public function deserialize(MemberNode $context)
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
