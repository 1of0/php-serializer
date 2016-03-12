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
 * @Target({"CLASS", "PROPERTY", "METHOD"})
 */
class Converter extends Annotation
{
	/**
	 * @var string $value
	 */
	public $value;

	/**
	 * @var string $serializer
	 */
	public $serializer;

	/**
	 * @var string $deserializer
	 */
	public $deserializer;
}
