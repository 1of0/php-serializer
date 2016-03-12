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

use OneOfZero\Json\Contexts\MemberContext;
use OneOfZero\Json\Exceptions\ResumeSerializationException;

abstract class AbstractMemberConverter implements MemberConverterInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberContext $context)
	{
		throw new ResumeSerializationException();
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberContext $context)
	{
		throw new ResumeSerializationException();
	}
}
