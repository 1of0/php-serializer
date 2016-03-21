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
	public function serialize(MemberNode $node)
	{
		$object = $node->getValue();
		
		if ($object instanceof SimpleClass)
		{
			return implode('|', [ $object->foo, $object->bar, $object->baz ]);
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
	public function deserialize(MemberNode $node)
	{
		$class = $node->getMapper()->getType();
		
		if ($class === SimpleClass::class)
		{
			list($foo, $bar, $baz) = explode('|', $node->getSerializedValue());
			return new SimpleClass($foo, $bar, $baz);
		}

		if ($class === ReferableClass::class)
		{
			return new ReferableClass($node->getSerializedValue());
		}

		return null;
	}
}
