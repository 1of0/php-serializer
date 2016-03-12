<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Converters;

use DateTime;
use OneOfZero\Json\Contexts\MemberContext;

class DateTimeConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberContext $context)
	{
		$value = $context->getValue();
		
		return ($value instanceof DateTime) ? $value->getTimestamp() : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberContext $context)
	{
		if (ctype_digit($context->getSerializedValue()))
		{
			$date = new DateTime();
			$date->setTimestamp($context->getSerializedValue());
			return $date;
		}
		
		return null;
	}
}
