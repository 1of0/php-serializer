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
 * Specifies that the annotated class should be serialized and deserialized with the contract class that is specified 
 * as value.
 * 
 * @Annotation
 * @Target({"CLASS"})
 */
class Contract extends Annotation
{
	/**
	 * @var string $value
	 */
	public $value;
}
