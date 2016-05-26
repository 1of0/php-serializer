<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

/**
 * The Convert class is a static facade built around the singleton instance of the Serializer class.
 * 
 * This facade is only intended for prototyping and simple scripts. For larger applications it's recommended to manage 
 * a Serializer instance with dependency injection.
 * 
 * @see Serializer
 */
class Convert
{
	/**
	 * Returns the provided $data in JSON encoded form.
	 * 
	 * @param mixed $data
	 * 
	 * @return string
	 */
	public static function toJson($data)
	{
		return Serializer::get()->serialize($data);
	}

	/**
	 * Returns the data in the provided $json data as a PHP value.
	 * 
	 * If the $typeHint parameter is provided, the result will be an object of that type populated with the $json data.
	 * 
	 * @param string $json
	 * @param string|null $typeHint
	 * 
	 * @return mixed
	 */
	public static function fromJson($json, $typeHint = null)
	{
		return Serializer::get()->deserialize($json, $typeHint);
	}

	/**
	 * Casts the provided $instance into the specified $type by serializing the $instance and deserializing it into the
	 * specified $type. 
	 * 
	 * @param object $instance
	 * @param string $type
	 * 
	 * @return object
	 */
	public static function cast($instance, $type)
	{
		return Serializer::get()->cast($instance, $type);
	}
}
