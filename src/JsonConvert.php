<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;


class JsonConvert
{
	public static function toJson($data)
	{
		return Serializer::get()->serialize($data);
	}

	public static function fromJson($json, $typeHint = null)
	{
		return Serializer::get()->deserialize($json, $typeHint);
	}
}