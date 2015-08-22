<?php


namespace OneOfZero\Json\Internals;


class SerializationState
{
	/**
	 * @var object $parentObject
	 */
	public $parentObject;

	/**
	 * @var array $serializedParentState
	 */
	public $serializedParentState;

	/**
	 * @param object $parentObject
	 * @param array $serializedParentState
	 */
	public function __construct($parentObject, array $serializedParentState)
	{
		$this->parentObject = $parentObject;
		$this->serializedParentState = $serializedParentState;
	}
}