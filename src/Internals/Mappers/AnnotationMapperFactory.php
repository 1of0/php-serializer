<?php

namespace OneOfZero\Json\Internals\Mappers;

use OneOfZero\BetterAnnotations\Annotations;
use ReflectionClass;

class AnnotationMapperFactory implements MapperFactoryInterface
{
	/**
	 * @var Annotations $annotations
	 */
	private $annotations;

	/**
	 * @param Annotations $annotations
	 */
	public function __construct(Annotations $annotations)
	{
		$this->annotations = $annotations;
	}

	/**
	 * @param ReflectionClass $reflector
	 *
	 * @return ObjectMapperInterface
	 */
	public function mapObject(ReflectionClass $reflector)
	{
		return new ObjectAnnotationMapper($this->annotations);
	}
}