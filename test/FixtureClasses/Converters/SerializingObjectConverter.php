<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses\Converters;

use OneOfZero\Json\Converters\AbstractObjectConverter;
use OneOfZero\Json\Nodes\ObjectNode;

class SerializingObjectConverter extends AbstractObjectConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(ObjectNode $node)
	{
		return [ 'abcd' => 1234 ];
	}
}
