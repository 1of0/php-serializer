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
use OneOfZero\Json\Mappers\AbstractObjectMapper;

/**
 * Implementation of a mapper that maps the serialization metadata for a class using annotations.
 */
class AnnotationObjectMapper extends AbstractObjectMapper
{
	use AnnotationMapperTrait;

	public function isExplicitInclusionEnabled()
	{
		if ($this->getAnnotations()->has($this->getTarget(), ExplicitInclusion::class))
		{
			return true;
		}
		
		return parent::isExplicitInclusionEnabled();
	}

	public function isMetadataDisabled()
	{
		if ($this->getAnnotations()->has($this->getTarget(), NoMetadata::class))
		{
			return true;
		}
		
		return parent::isMetadataDisabled();
	}
}
