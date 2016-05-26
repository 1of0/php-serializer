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
 * Specified that the annotated member is an array. This is often needed for deserialization.
 * 
 * @Annotation
 * @Target({"PROPERTY","METHOD"})
 */
class IsArray extends Annotation
{

}
