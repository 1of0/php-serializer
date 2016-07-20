<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

/**
 * Defines an interface for an object that is referable by other objects. By annotation, mapping or serializer 
 * configuration, the serializer can be configured to replace the object with the identifier in the JSON representation.
 */
interface ReferableInterface
{
	/**
	 * @return mixed
	 */
	public function getId();
}
