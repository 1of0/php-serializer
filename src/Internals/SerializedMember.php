<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;

class SerializedMember
{
	/**
	 * @var string $propertyName
	 */
	public $propertyName;

	/**
	 * @var mixed $value
	 */
	public $value;

	/**
	 * @param string $propertyName
	 * @param mixed $value
	 */
	public function __construct($propertyName = null, $value = null)
	{
		$this->propertyName = $propertyName;
		$this->value = $value;
	}

	/**
	 * @param string $metaType
	 * @return mixed|null
	 */
	public function getMetadata($metaType)
	{
		return Metadata::get($this->value, $metaType);
	}

	/**
	 * @param string $metaType
	 * @return bool
	 */
	public function containsMetadata($metaType)
	{
		return Metadata::contains($this->value, $metaType);
	}

	/**
	 * @param string $metaType
	 * @param mixed $metaValue
	 */
	public function setMetadata($metaType, $metaValue)
	{
		Metadata::set($this->value, $metaType, $metaValue);
	}

}
