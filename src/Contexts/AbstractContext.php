<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Contexts;

abstract class AbstractContext
{
	/**
	 * @var AbstractContext|null $parent
	 */
	protected $parent;

	/**
	 * @param AbstractContext|null $parent
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
	 * @return AbstractContext|null
	 */
	public function getParent()
	{
		return $this->parent;
	}
}
