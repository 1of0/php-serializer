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
	 * @param mixed $data
	 *
	 * @return string
	 */
	public function serialize($data);

	/**
	 * @param string $json
	 * @param string|null $typeHint
	 *
	 * @return mixed
	 */
	public function deserialize($json, $typeHint = null);
}
