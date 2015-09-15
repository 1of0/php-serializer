<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;

use InvalidArgumentException;
use stdClass;

class Metadata
{
	const TYPE = '@class';
	const ID = 'id';

	/**
	 * @param mixed $target
	 * @param string $metaType
	 * @param mixed $metaValue
	 */
	public static function set(&$target, $metaType, $metaValue)
	{
		if ($target === null)
		{
			throw new InvalidArgumentException("Null provided as target for metadata storage");
		}

		if ($target instanceof stdClass)
		{
			$target->$metaType = $metaValue;
			return;
		}

		if (is_array($target))
		{
			$target[$metaType] = $metaValue;
			return;
		}

		throw new InvalidArgumentException("Metadata can only be stored in arrays and stdClass objects");
	}

	/**
	 * @param mixed $target
	 * @param string $metaType
	 * @return bool
	 */
	public static function contains(&$target, $metaType)
	{
		return $target !== null
		    && (is_array($target) || $target instanceof stdClass)
		    && array_key_exists($metaType, $target)
		;
	}

	/**
	 * @param mixed $target
	 * @param string $metaType
	 * @return mixed|null
	 */
	public static function get(&$target, $metaType)
	{
		if (!self::contains($target, $metaType))
		{
			return null;
		}

		$targetAsArray = (array)$target;
		return $targetAsArray[$metaType];
	}
}
