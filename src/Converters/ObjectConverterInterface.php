<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Converters;

use OneOfZero\Json\Nodes\AbstractObjectNode;
use OneOfZero\Json\Exceptions\ResumeSerializationException;

interface ObjectConverterInterface
{
	/**
	 * Should return a representation of the instance in the provided object node.
	 *
	 * The return value should be a type or structure that is serializable by json_encode().
	 *
	 * @param AbstractObjectNode $node
	 *
	 * @return mixed
	 *
	 * @throws ResumeSerializationException May be thrown to indicate that the serializer should resume with the regular
	 *                                      serialization strategy. This can be useful to avoid recursion.
	 */
	public function serialize(AbstractObjectNode $node);

	/**
	 * Should return a deserialized representation of the serialized instance in the provided object node.
	 *
	 * @param AbstractObjectNode $node
	 *
	 * @return mixed
	 *
	 * @throws ResumeSerializationException May be thrown to indicate that the serializer should resume with the regular
	 *                                      deserialization strategy. This can be useful to avoid recursion.
	 */
	public function deserialize(AbstractObjectNode $node);
}
