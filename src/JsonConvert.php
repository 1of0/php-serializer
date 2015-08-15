<?php


namespace OneOfZero\Json;


class JsonConvert
{
	public static function toJson($data)
	{
		return Serializer::get()->serialize($data);
	}

	public static function fromJson($json)
	{
		return Serializer::get()->deserialize($json);
	}
}