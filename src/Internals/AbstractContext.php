<?php

namespace OneOfZero\Json\Internals;

abstract class AbstractContext
{
	/**
	 * @var AbstractContext|null $parent
	 */
	protected $parent;

	/**
	 * @param AbstractContext $parent
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