<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Helpers;

class Flags
{
	/**
	 * @param int $value
	 * @param int $flags
	 *
	 * @return bool
	 */
	public static function has($value, $flags)
	{
		return ($value & $flags) === $flags;
	}

	/**
	 * @param int $value
	 * @param int $flags
	 *
	 * @return int
	 */
	public static function add($value, $flags)
	{
		return $value | $flags;
	}

	/**
	 * @param int $value
	 * @param int $flags
	 *
	 * @return int
	 */
	public static function remove($value, $flags)
	{
		return $value & (~$flags);
	}

	/**
	 * @param int $value
	 * @param int $flags
	 *
	 * @return int
	 */
	public static function toggle($value, $flags)
	{
		return $value ^ $flags;
	}

	/**
	 * @param int $value
	 *
	 * @return int
	 */
	public static function invert($value)
	{
		return ~$value;
	}
}
