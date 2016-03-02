<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

trait BaseFactoryTrait
{
	/**
	 * Holds an instance of the parent mapper factory
	 * 
	 * @var MapperFactoryInterface $parent
	 */
	protected $parent;

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setParent(MapperFactoryInterface $parent)
	{
		$this->parent = $parent;
	}
}
