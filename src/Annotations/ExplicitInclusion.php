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
 * Specifies that all members in the annotated class must be explicitly included by means of annotations/mappings.
 * 
 * @Annotation
 * @Target({"CLASS"})
 */
class ExplicitInclusion extends Annotation
{

}
