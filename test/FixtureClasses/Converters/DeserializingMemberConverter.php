<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses\Converters;

use OneOfZero\Json\Contexts\MemberContext;
use OneOfZero\Json\Converters\AbstractMemberConverter;

class DeserializingMemberConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberContext $context)
	{
		return 'bar';
	}
}
