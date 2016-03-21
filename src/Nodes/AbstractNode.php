<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Nodes;

abstract class AbstractNode
{
	/**
	 * @var AbstractNode|null $parent
	 */
	protected $parent;

	/**
	 * @param AbstractNode|null $parent
	 *
	 * @return self
	 */
	public function withParent($parent)
	{
		$new = clone $this;
		$new->parent = $parent;
		return $new;
	}

	/**
	 * @return AbstractNode|null
	 */
	public function getParent()
	{
		return $this->parent;
	}
}
