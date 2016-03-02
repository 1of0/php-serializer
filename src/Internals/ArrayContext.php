<?php

namespace OneOfZero\Json\Internals;

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
	 *
	 * @return self
	 */
	public function withSerializedArrayValue($value)
	{
		$new = clone $this;
		$new->serializedArray[] = $value;
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