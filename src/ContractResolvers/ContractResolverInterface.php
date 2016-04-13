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
	public function createSerializingObjectContract(AbstractObjectNode $object);

	/**
	 * @param AbstractObjectNode $object
	 *
	 * @return ObjectMapperInterface|ContractObjectMapper
	 */
	public function createDeserializingObjectContract(AbstractObjectNode $object);

	/**
	 * @param MemberNode $member
	 * 
	 * @return MemberMapperInterface|ContractMemberMapper
	 */
	public function createSerializingMemberContract(MemberNode $member);

	/**
	 * @param MemberNode $member
	 *
	 * @return MemberMapperInterface|ContractMemberMapper
	 */
	public function createDeserializingMemberContract(MemberNode $member);
}
