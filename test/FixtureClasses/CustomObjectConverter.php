<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\AbstractObjectConverter;
use OneOfZero\Json\Internals\ObjectContext;

class CustomObjectConverter extends AbstractObjectConverter
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
