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
