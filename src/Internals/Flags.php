<?php

namespace OneOfZero\Json\Internals;

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