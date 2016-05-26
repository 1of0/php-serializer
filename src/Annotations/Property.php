<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * Explicitly includes the annotated property, and optionally allows to specify a name different than the property 
 * name, and allows to specify whether the property is only serialized or deserialized.
 * 
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Property extends AbstractName
{
	/**
	 * @var bool $serialize
	 */
	public $serialize = true;

	/**
	 * @var bool $deserialize
	 */
	public $deserialize = true;
}
