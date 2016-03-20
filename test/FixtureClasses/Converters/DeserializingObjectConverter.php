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

class DeserializingObjectConverter extends AbstractObjectConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function deserialize(ObjectContext $context)
	{
		$context->getInstance()->foo = 'bar';
		return $context->getInstance();
	}
}
