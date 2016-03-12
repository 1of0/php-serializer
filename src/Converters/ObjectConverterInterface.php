<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Converters;

use OneOfZero\Json\Contexts\ObjectContext;
use OneOfZero\Json\Exceptions\ResumeSerializationException;

interface ObjectConverterInterface
{
	/**
	 * Should return a representation of the instance in the provided object context.
	 *
	 * The return value should be a type or structure that is serializable by json_encode().
	 *
	 * @param ObjectContext $context
	 *
	 * @return mixed
	 *
	 * @throws ResumeSerializationException May be thrown to indicate that the serializer should resume with the regular
	 *                                      serialization strategy. This can be useful to avoid recursion.
	 */
	public function serialize(ObjectContext $context);

	/**
	 * Should return a deserialized representation of the serialized instance in the provided object context.
	 *
	 * @param ObjectContext $context
	 *
	 * @return mixed
	 *
	 * @throws ResumeSerializationException May be thrown to indicate that the serializer should resume with the regular
	 *                                      deserialization strategy. This can be useful to avoid recursion.
	 */
	public function deserialize(ObjectContext $context);
}
