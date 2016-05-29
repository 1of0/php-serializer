<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

abstract class AbstractFactory implements FactoryInterface
{
	/**
	 * @var SourceInterface $source
	 */
	protected $source;
	
	/**
	 * @param SourceInterface $source
	 */
	public function __construct(SourceInterface $source = null)
	{
		$this->source = $source;
	}
	
	function __clone()
	{
		if ($this->source !== null)
		{
			$this->source = clone $this->source;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSource()
	{
		return $this->source;
	}


}
