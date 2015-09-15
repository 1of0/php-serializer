<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;

use Doctrine\Common\Annotations\Annotation;

/**
 * Provides annotation helper methods, given that annotations are stored in the $annotations field.
 */
trait AnnotationHelperTrait
{
	/**
	 * @var Annotation[] $annotations
	 */
	protected $annotations = [];

	/**
	 * Returns a boolean value indicating whether the annotation is declared.
	 *
	 * @param string $annotationClass
	 * @return bool
	 */
	public function hasAnnotation($annotationClass)
	{
		return $this->getAnnotation($annotationClass) !== null;
	}

	/**
	 * Returns the first found annotation that is an instance of the provided $annotationClass. If no such annotation is
	 * found, this function will return null.
	 *
	 * @param string $annotationClass
	 * @return Annotation|null
	 */
	public function getAnnotation($annotationClass)
	{
		foreach ($this->annotations as $annotation)
		{
			if ($annotation instanceof $annotationClass)
			{
				return $annotation;
			}
		}
		return null;
	}

	/**
	 * Returns either all detected annotations, or all annotations that are instances of the optionally provided
	 * $annotationClass. If no annotations are found, an empty array is returned.
	 *
	 * @param null|string $annotationClass
	 * @return Annotation[]
	 */
	public function getAnnotations($annotationClass = null)
	{
		if ($annotationClass === null)
		{
			return $this->annotations;
		}

		$annotations = [];
		foreach ($this->annotations as $annotation)
		{
			if ($annotation instanceof $annotationClass)
			{
				$annotations[] = $annotation;
			}
		}
		return $annotations;
	}
}
