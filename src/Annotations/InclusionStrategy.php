<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class InclusionStrategy extends Annotation
{
	const IMPLICIT = 0;
	const EXPLICIT = 1;

	/**
	 * @var int $strategy
	 */
	public $strategy;
}