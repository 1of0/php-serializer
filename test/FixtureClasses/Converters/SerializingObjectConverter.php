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

class SerializingObjectConverter extends AbstractObjectConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(ObjectContext $context)
	{
		return [ 'abcd' => 1234 ];
	}
}
