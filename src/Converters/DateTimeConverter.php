<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Converters;

use Carbon\Carbon;
use DateTime;
use OneOfZero\Json\Exceptions\ResumeSerializationException;
use OneOfZero\Json\Nodes\MemberNode;

class DateTimeConverter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberNode $node, $typeHint = null)
	{
		$value = $node->getValue();
		
		if ($value === null || !($value instanceof DateTime))
		{
			throw new ResumeSerializationException();
		}
		
		return $value->getTimestamp();
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberNode $node, $typeHint = null)
	{
		if ($typeHint === null || !ctype_digit($node->getSerializedValue()))
		{
			throw new ResumeSerializationException();	
		}
		
		if ($typeHint !== DateTime::class && !is_subclass_of($typeHint, DateTime::class))
		{
			throw new ResumeSerializationException();
		}
		
		if (class_exists(Carbon::class) && $typeHint === Carbon::class)
		{
			return Carbon::createFromTimestamp($node->getSerializedValue());
		}
		
		$date = new DateTime();
		$date->setTimestamp($node->getSerializedValue());
		
		return $date;
	}
}
