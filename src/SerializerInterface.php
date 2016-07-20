<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

interface SerializerInterface
{
	/**
	 * Returns the provided $data in JSON encoded form.
	 * 
	 * @param mixed $data
	 *
	 * @return string
	 */
	public function serialize($data);

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
	public function deserialize($json, $typeHint = null);
}
