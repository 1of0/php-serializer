<?php

namespace OneOfZero\Json\Mappers\Null;

use OneOfZero\Json\Mappers\MapperChainInterface;
use OneOfZero\Json\Mappers\MapperInterface;

class NullChain implements MapperChainInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getConfiguration()
	{
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTarget()
	{
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTop()
	{
		
	}

	/**
	 * {@inheritdoc}
	 */
	public function getNext(MapperInterface $caller)
	{
		// TODO: Implement getNext() method.
	}
}
