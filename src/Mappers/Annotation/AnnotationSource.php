<?php

namespace OneOfZero\Json\Mappers\Annotation;

use Doctrine\Common\Annotations\Reader;
use OneOfZero\BetterAnnotations\Annotations;
use OneOfZero\Json\Mappers\SourceInterface;

class AnnotationSource implements SourceInterface
{
	/**
	 * @var Annotations $annotations
	 */
	private $annotations;

	/**
	 * @param Reader $annotationReader
	 */
	public function __construct(Reader $annotationReader)
	{
		$this->annotations = new Annotations($annotationReader);
	}

	/**
	 * @return Annotations
	 */
	public function getAnnotations()
	{
		return $this->annotations;
	}

	public function getHash()
	{
		return sha1(__CLASS__);
	}
}
