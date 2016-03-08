<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

use OneOfZero\Json\Configuration;

trait BaseFactoryTrait
{
	/**
	 * Holds an instance of the serializer configuration.
	 *
	 * @var Configuration $configuration
	 */
	protected $configuration;

	/**
	 * Holds an instance of the parent mapper factory
	 * 
	 * @var MapperFactoryInterface $parent
	 */
	protected $parent;

	/**
	 *
	 */
	public function __clone()
	{
		if ($this->parent !== null)
		{
			$this->parent = clone $this->parent;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withConfiguration(Configuration $configuration)
	{
		$new = clone $this;

		$new->configuration = $configuration;

		if ($new->parent !== null)
		{
			$new->parent = $new->parent->withConfiguration($configuration);
		}

		return $new;
	}

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
	public function withParent(MapperFactoryInterface $parent)
	{
		$new = clone $this;
		$new->parent = $parent;
		return $new;
	}
}
