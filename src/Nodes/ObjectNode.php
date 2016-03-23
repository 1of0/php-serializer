<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Nodes;

use ReflectionClass;

class ObjectNode extends AbstractObjectNode
{
	/**
	 * @var ReflectionClass $reflector
	 */
	private $reflector;

	/**
	 * @param ReflectionClass $reflector
	 *
	 * @return self
	 */
	public function withReflector(ReflectionClass $reflector)
	{
		$new = clone $this;
		$new->reflector = $reflector;
		return $new;
	}
	
	/**
	 * @return ReflectionClass
	 */
	public function getReflector()
	{
		return $this->reflector;
	}
}
