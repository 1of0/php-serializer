<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Converters;

use OneOfZero\Json\Nodes\MemberNode;
use OneOfZero\Json\Exceptions\ResumeSerializationException;

interface MemberConverterInterface
{
	/**
	 * Should return a representation of the member value in the provided member node.
	 * 
	 * The $typeHint argument may be null if the type could not be detected or inferred.
	 *
	 * The return value should be a type or structure that is serializable by json_encode().
	 *
	 * @param MemberNode $node
	 * @param string|null $typeHint
	 * 
	 * @return mixed
	 * 
	 * @throws ResumeSerializationException May be thrown to indicate that the serializer should resume with the regular
	 *                                      serialization strategy. This can be useful to avoid recursion.
	 */
	public function serialize(MemberNode $node, $typeHint = null);

	/**
	 * Should return a deserialized representation of the serialized value in the provided member node.
	 *
	 * The $typeHint argument may be null if the type could not be detected or inferred.
	 *
	 * @param MemberNode $node
	 * @param string|null $typeHint
	 *
	 * @return mixed
	 *
	 * @throws ResumeSerializationException May be thrown to indicate that the serializer should resume with the regular
	 *                                      deserialization strategy. This can be useful to avoid recursion.
	 */
	public function deserialize(MemberNode $node, $typeHint = null);
}
