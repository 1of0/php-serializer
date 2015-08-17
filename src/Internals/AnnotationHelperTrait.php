<?php


namespace OneOfZero\Json\Internals;


use Doctrine\Common\Annotations\Annotation;

trait AnnotationHelperTrait
{
	/**
	 * @var Annotation[] $annotations
	 */
	protected $annotations = [];

	/**
	 * @param string $annotationClass
	 * @return bool
	 */
	public function hasAnnotation($annotationClass)
	{
		return !is_null($this->getAnnotation($annotationClass));
	}

	/**
	 * @param null|string $annotationClass
	 * @return Annotation[]
	 */
	public function getAnnotations($annotationClass = null)
	{
		if (is_null($annotationClass))
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

	/**
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
}