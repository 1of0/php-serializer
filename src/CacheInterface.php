<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

/**
 * Interface CacheInterface
 * @package OneOfZero\Json
 *
 * Describes members of a simple persistent key value storage.
 */
interface CacheInterface
{
	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function store($key, $value);

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key);
}