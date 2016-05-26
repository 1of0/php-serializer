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
 * Specifies that the annotated member is a referable, and may be replaced by a reference during serialization.
 * 
 * @Annotation
 * @Target({"PROPERTY","METHOD"})
 */
class IsReference extends Annotation
{
	/**
	 * @var bool $lazy
	 */
	public $lazy = false;
}
