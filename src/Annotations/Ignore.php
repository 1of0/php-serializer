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
 * @Target({"PROPERTY"})
 */
class Ignore extends Annotation
{
	/**
	 * @var bool $ignoreOnSerialize
	 */
	public $ignoreOnSerialize = true;

	/**
	 * @var bool $ignoreOnDeserialize
	 */
	public $ignoreOnDeserialize = true;
}
