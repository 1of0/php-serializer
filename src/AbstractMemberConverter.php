<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

use OneOfZero\Json\Exceptions\ResumeSerializationException;
use OneOfZero\Json\Internals\MemberContext;

abstract class AbstractMemberConverter
{
	/**
	 * Should return a representation of the member value in the provided member context.
	 *
	 * The return value should be a type or structure that is serializable by json_encode().
	 *
	 * @param MemberContext $context
	 *
	 * @return mixed
	 *
	 * @throws ResumeSerializationException May be thrown to indicate that the serializer should resume with the regular
	 *                                      serialization strategy. This can be useful to avoid recursion.
	 */
	public function serialize(MemberContext $context)
	{
		throw new ResumeSerializationException();
	}

	/**
	 * Should return a deserialized representation of the serialized value in the provided member context.
	 *
	 * @param MemberContext $context
	 *
	 * @return mixed
	 *
	 * @throws ResumeSerializationException May be thrown to indicate that the serializer should resume with the regular
	 *                                      deserialization strategy. This can be useful to avoid recursion.
	 */
	public function deserialize(MemberContext $context)
	{
		throw new ResumeSerializationException();
	}
}
