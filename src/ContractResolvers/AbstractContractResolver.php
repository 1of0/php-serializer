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

abstract class AbstractContractResolver implements ContractResolverInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function createSerializingObjectContract(AbstractObjectNode $object)
	{
		return new ContractObjectMapper();
	}

	/**
	 * {@inheritdoc}
	 */
	public function createDeserializingObjectContract(AbstractObjectNode $object)
	{
		return new ContractObjectMapper();
	}

	/**
	 * {@inheritdoc}
	 */
	public function createSerializingMemberContract(MemberNode $member)
	{
		return new ContractMemberMapper();
	}

	/**
	 * {@inheritdoc}
	 */
	public function createDeserializingMemberContract(MemberNode $member)
	{
		return new ContractMemberMapper();
	}
}
