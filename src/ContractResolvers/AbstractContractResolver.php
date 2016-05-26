<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\ContractResolvers;

use OneOfZero\Json\Mappers\ContractMemberMapper;
use OneOfZero\Json\Mappers\ContractObjectMapper;
use OneOfZero\Json\Nodes\AbstractObjectNode;
use OneOfZero\Json\Nodes\MemberNode;

/**
 * Empty abstract implementation of a contract resolver.
 */
abstract class AbstractContractResolver implements ContractResolverInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function createSerializingObjectContract(AbstractObjectNode $object)
	{
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function createDeserializingObjectContract(AbstractObjectNode $object)
	{
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function createSerializingMemberContract(MemberNode $member)
	{
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function createDeserializingMemberContract(MemberNode $member)
	{
		return null;
	}
}
