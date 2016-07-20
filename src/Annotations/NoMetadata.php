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
 * Specifies that the annotated class should not include type hinting metadata when serialized.
 * 
 * @Annotation
 * @Target({"CLASS"})
 */
class NoMetadata extends Annotation
{

}
