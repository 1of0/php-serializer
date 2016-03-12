<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Contexts;

class ArrayContext extends AbstractContext
{
	/**
	 * @var array $array
	 */
	private $array;

	/**
	 * @var array $serializedArray
	 */
	private $serializedArray;

	/**
	 * @param mixed $value
	 * @param string|null $key
	 *
	 * @return static
	 */
	public function withArrayValue($value, $key = null)
	{
		$new = clone $this;

		if (func_num_args() === 1)
		{
			$new->array[] = $value;
		}
		else
		{
			$new->array[$key] = $value;
		}

		return $new;
	}

	/**
	 * @param mixed $value
	 * @param string|null $key
	 *
	 * @return static
	 */
	public function withSerializedArrayValue($value, $key = null)
	{
		$new = clone $this;

		if (func_num_args() === 1)
		{
			$new->serializedArray[] = $value;
		}
		else
		{
			$new->serializedArray[$key] = $value;
		}

		return $new;
	}

	#region // Generic immutability helpers

	/**
	 * @param array $array
	 *
	 * @return self
	 */
	public function withArray(array $array)
	{
		$new = clone $this;
		$new->array = $array;
		return $new;
	}

	/**
	 * @param array $array
	 *
	 * @return self
	 */
	public function withSerializedArray(array $array)
	{
		$new = clone $this;
		$new->serializedArray = $array;
		return $new;
	}

	#endregion

	#region // Generic getters and setters

	/**
	 * @return array
	 */
	public function getArray()
	{
		return $this->array;
	}

	/**
	 * @return array
	 */
	public function getSerializedArray()
	{
		return $this->serializedArray;
	}

	#endregion
}
