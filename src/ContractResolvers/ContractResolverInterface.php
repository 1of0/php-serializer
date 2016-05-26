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

/**
 * Defines the interface for a contract resolver. 
 * 
 * A contract resolver allows you to overlay your own mapper over the pipeline for each node visited by the serializer 
 * and deserializer. While setting up your own mapper may sound complicated, there are the ContractObjectMapper and 
 * ContractMemberMapper classes that allow you to handset the mapper values without needing to write your own mappers.
 * 
 * For an example contract resolver see the PropertyNameContractResolver class.
 */
interface ContractResolverInterface
{
	/**
	 * Should return a mapper for the provided object node.
	 * 
	 * @param AbstractObjectNode $object
	 * 
	 * @return ObjectMapperInterface|ContractObjectMapper|null
	 */
	public function createSerializingObjectContract(AbstractObjectNode $object);

	/**
	 * Should return a mapper for the provided object node.
	 * 
	 * @param AbstractObjectNode $object
	 *
	 * @return ObjectMapperInterface|ContractObjectMapper|null
	 */
	public function createDeserializingObjectContract(AbstractObjectNode $object);

	/**
	 * Should return a mapper for the provided member node.
	 * 
	 * @param MemberNode $member
	 * 
	 * @return MemberMapperInterface|ContractMemberMapper|null
	 */
	public function createSerializingMemberContract(MemberNode $member);

	/**
	 * Should return a mapper for the provided member node.
	 * 
	 * @param MemberNode $member
	 *
	 * @return MemberMapperInterface|ContractMemberMapper|null
	 */
	public function createDeserializingMemberContract(MemberNode $member);
}
