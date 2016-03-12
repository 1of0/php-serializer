<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\ExplicitInclusion;
use OneOfZero\Json\Annotations\NoMetadata;

/**
 * Implementation of a mapper that maps the serialization metadata for a class using annotations.
 */
class AnnotationObjectMapper implements ObjectMapperInterface
{
	use BaseObjectMapperTrait;
	use AnnotationMapperTrait;

	public function wantsExplicitInclusion()
	{
		if ($this->annotations->has($this->target, ExplicitInclusion::class))
		{
			return true;
		}
		
		return $this->getBase()->wantsExplicitInclusion();
	}

	public function wantsNoMetadata()
	{
		if ($this->annotations->has($this->target, NoMetadata::class))
		{
			return true;
		}
		
		return $this->getBase()->wantsNoMetadata();
	}
}
