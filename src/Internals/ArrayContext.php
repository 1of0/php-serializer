<?php

namespace OneOfZero\Json\Internals;

class ArrayContext
{
	/**
	 * @var array $array
	 */
	public $array;

	/**
	 * @var array $serializedArray
	 */
	public $serializedArray;

	/**
	 * @var MemberContext|null $parentContext
	 */
	private $parentContext;

	/**
	 * @param array $array
	 * @param array $serializedArray
	 * @param MemberContext|null $parentContext
	 */
	public function __construct(array $array, array $serializedArray, $parentContext)
	{
		$this->array = $array;
		$this->serializedArray = $serializedArray;
		$this->parentContext = $parentContext;
	}

	/**
	 * @return MemberContext|null
	 */
	public function getParentContext()
	{
		return $this->parentContext;
	}
}