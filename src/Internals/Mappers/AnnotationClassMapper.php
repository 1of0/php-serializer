<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals\Mappers;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use OneOfZero\Json\Annotations;

class AnnotationClassMapper extends AbstractClassMapper
{
	/**
	 * @var AnnotationReader $reader
	 */
	private $reader;

	/**
	 * @param AnnotationReader $reader
	 */
	public function __construct(AnnotationReader $reader)
	{
		$this->reader = $reader;
	}

	/**
	 *
	 */
	public function map()
	{
		$this->explicitInclusion = $this->hasAnnotation(Annotations\ExplicitInclusion::class);
		$this->noMetadata = $this->hasAnnotation(Annotations\NoMetadata::class);
	}

	/**
	 * @return AbstractFieldMapper
	 */
	protected function getFieldMapper()
	{
		return new AnnotationFieldMapper($this->reader);
	}

	/**
	 * @param string $annotationClass
	 * @return bool
	 */
	private function hasAnnotation($annotationClass)
	{
		return $this->reader->getClassAnnotation($this->target, $annotationClass) !== null;
	}
}
