<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Annotation;

use Doctrine\Common\Annotations\Annotation;
use OneOfZero\Json\Annotations\ExplicitInclusion;
use OneOfZero\Json\Annotations\NoMetadata;
use OneOfZero\Json\Mappers\BaseObjectMapperTrait;
use OneOfZero\Json\Mappers\ObjectMapperInterface;

/**
 * Implementation of a mapper that maps the serialization metadata for a class using annotations.
 */
class AnnotationObjectMapper implements ObjectMapperInterface
{
	use BaseObjectMapperTrait;
	use AnnotationMapperTrait;

	public function isExplicitInclusionEnabled()
	{
		if ($this->annotations->has($this->target, ExplicitInclusion::class))
		{
			return true;
		}
		
		return $this->getBase()->isExplicitInclusionEnabled();
	}

	public function isMetadataDisabled()
	{
		if ($this->annotations->has($this->target, NoMetadata::class))
		{
			return true;
		}
		
		return $this->getBase()->isMetadataDisabled();
	}
}
