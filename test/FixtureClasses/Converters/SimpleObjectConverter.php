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
	public function serialize(ObjectNode $context)
	{
		return ['abcd' => $context->getInstance()->foo];
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(ObjectNode $context)
	{
		$instance = $context->getReflector()->newInstance();
		$instance->foo = $context->getSerializedMemberValue('abcd');
		
		return $instance;
	}
}
