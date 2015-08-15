<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Repository extends Annotation
{
	/**
	 * @var string $value
	 */
	public $value;
}