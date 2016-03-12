<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses\Converters;

use OneOfZero\Json\Contexts\ObjectContext;
use OneOfZero\Json\Converters\AbstractObjectConverter;

class SimpleObjectConverter extends AbstractObjectConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(ObjectContext $context)
	{
		return ['abc' => $context->getInstance()->foo];
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(ObjectContext $context)
	{
		$instance = $context->getReflector()->newInstance();
		$instance->foo = $context->getSerializedMemberValue('abc');
		
		return $instance;
	}
}
