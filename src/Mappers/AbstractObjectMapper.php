<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

abstract class AbstractObjectMapper extends AbstractMapper implements ObjectMapperInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function mapMembers()
	{
		return $this->getChain()->mapMembers();
	}
	
	#region // Forwards to next mapper in chain
	
	/**
	 * {@inheritdoc}
	 */
	public function isExplicitInclusionEnabled()
	{
		return $this->getChain()->getNext($this)->isExplicitInclusionEnabled();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isMetadataDisabled()
	{
		return $this->getChain()->getNext($this)->isMetadataDisabled();
	}
	
	#endregion
}
