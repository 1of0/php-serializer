<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY", "METHOD"})
 */
class CustomConverter extends Annotation
{
	/**
	 * @var string $value
	 */
	public $value;

	/**
	 * @var bool $serialize
	 */
	public $serialize = true;

	/**
	 * @var bool $deserialize
	 */
	public $deserialize = true;
}