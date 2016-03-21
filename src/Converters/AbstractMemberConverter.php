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

use OneOfZero\Json\Nodes\MemberNode;
use OneOfZero\Json\Exceptions\ResumeSerializationException;

abstract class AbstractMemberConverter implements MemberConverterInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberNode $context)
	{
		throw new ResumeSerializationException();
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberNode $context)
	{
		throw new ResumeSerializationException();
	}
}
