<?php


namespace OneOfZero\Json\Internals;


class DeserializationState
{
	/**
	 * @var array $serializedParentState
	 */
	public $serializedParentState;

	/**
	 * @var object $parentObject
	 */
	public $deserializedParentState;

	/**
	 * @param array $serializedParentState
	 * @param object $deserializedParentState
	 */
	public function __construct(array $serializedParentState, $deserializedParentState)
	{
		$this->serializedParentState = $serializedParentState;
		$this->deserializedParentState = $deserializedParentState;
	}
}