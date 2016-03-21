<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Nodes;

class AnonymousObjectNode extends AbstractObjectNode
{
	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function withInstanceMember($name, $value)
	{
		$new = clone $this;
		$new->instance->{$name} = $value;
		return $new;
	}
}
