<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses\Converters;

use OneOfZero\Json\Nodes\ObjectNode;
use OneOfZero\Json\Converters\AbstractObjectConverter;

class SimpleObjectConverter extends AbstractObjectConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(ObjectNode $node)
	{
		return ['abcd' => $node->getInstance()->foo];
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(ObjectNode $node)
	{
		$instance = $node->getReflector()->newInstance();
		$instance->foo = $node->getSerializedMemberValue('abcd');
		
		return $instance;
	}
}
