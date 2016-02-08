<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

use Doctrine\Common\Annotations\Annotation;
use OneOfZero\BetterAnnotations\Annotations;
use OneOfZero\Json\Annotations\ExplicitInclusion;
use OneOfZero\Json\Annotations\NoMetadata;

class AnnotationClassMapper extends AbstractClassMapper
{
	/**
	 * @var Annotations $annotations
	 */
	private $annotations;

	/**
	 * @param AnnotationReader $reader
	 */
	public function __construct(Annotations $reader)
	{
		$this->annotations = $reader;
	}

	public function wantsExplicitInclusion()
	{
		return $this->annotations->has($this->target, ExplicitInclusion::class);
	}

	public function wantsNoMetadata()
	{
		return $this->annotations->has($this->target, NoMetadata::class);
	}

	/**
	 * @return AbstractFieldMapper
	 */
	protected function getFieldMapper()
	{
		return new AnnotationFieldMapper($this->annotations);
	}
}
