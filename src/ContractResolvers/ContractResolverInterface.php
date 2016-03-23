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
use OneOfZero\Json\Mappers\MemberMapperInterface;
use OneOfZero\Json\Mappers\ObjectMapperInterface;
use OneOfZero\Json\Nodes\AbstractObjectNode;
use OneOfZero\Json\Nodes\MemberNode;

interface ContractResolverInterface
{
	/**
	 * @param AbstractObjectNode $object
	 * 
	 * @return ObjectMapperInterface|ContractObjectMapper
	 */
	public function createObjectContract(AbstractObjectNode $object);

	/**
	 * @param MemberNode $member
	 * 
	 * @return MemberMapperInterface|ContractMemberMapper
	 */
	public function createMemberContract(MemberNode $member);
}
