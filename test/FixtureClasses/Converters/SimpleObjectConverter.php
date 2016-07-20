<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses\Converters;

use OneOfZero\Json\Nodes\AbstractObjectNode;
use OneOfZero\Json\Nodes\ObjectNode;
use OneOfZero\Json\Converters\AbstractObjectConverter;

class SimpleObjectConverter extends AbstractObjectConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(AbstractObjectNode $node)
	{
		return ['abcd' => $node->getInstance()->foo];
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(AbstractObjectNode $node)
	{
		if (!($node instanceof ObjectNode))
		{
			return null;
		}

		$instance = $node->getReflector()->newInstance();
		$instance->foo = $node->getSerializedMemberValue('abcd');

		return $instance;
	}
}
