<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Nodes;

use OneOfZero\Json\Mappers\Anonymous\AnonymousObjectMapper;
use stdClass;

class AnonymousObjectNode extends AbstractObjectNode
{
	/**
	 * @param stdClass $instance
	 * 
	 * @return static
	 */
	public static function fromInstance($instance)
	{
		return (new AnonymousObjectNode)
			->withInstance($instance)
			->withSerializedInstance(new stdClass())
			->withMapper(new AnonymousObjectMapper($instance))
		;
	}

	/**
	 * @param stdClass $instance
	 * 
	 * @return static
	 */
	public static function fromSerializedInstance($instance)
	{
		return (new AnonymousObjectNode)
			->withInstance(new stdClass())
			->withSerializedInstance($instance)
			->withMapper(new AnonymousObjectMapper($instance))
		;
	}
}
