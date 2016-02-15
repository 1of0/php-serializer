<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

use OneOfZero\Json\Exceptions\ResumeSerializationException;
use OneOfZero\Json\Internals\ObjectContext;

abstract class AbstractObjectConverter
{
	/**
	 * Should return a serialized representation of the instance in the provided object context.
	 *
	 * @param ObjectContext $context
	 *
	 * @return string
	 *
	 * @throws ResumeSerializationException May be thrown to indicate that the serializer should resume with the regular
	 *                                      serialization strategy. This can be useful to avoid recursion.
	 */
	public function serialize(ObjectContext $context)
	{
		throw new ResumeSerializationException();
	}

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
	public function deserialize(ObjectContext $context)
	{

		throw new ResumeSerializationException();
	}
}
