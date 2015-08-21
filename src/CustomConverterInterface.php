<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;


interface CustomConverterInterface
{
	/**
	 * @param string $class
	 * @return bool
	 */
	public function canConvert($class);

	/**
	 * @param mixed $object
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @param mixed $objectContext
	 * @return string
	 */
	public function serialize($object, $propertyName, $propertyClass, $objectContext);

	/**
	 * @param mixed $data
	 * @param string $propertyName
	 * @param string $propertyClass
	 * @param array $objectContext
	 * @return mixed
	 */
	public function deserialize($data, $propertyName, $propertyClass, array $objectContext);
}