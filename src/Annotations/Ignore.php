<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Ignore extends Annotation
{
	/**
	 * @var bool $ignoreOnSerialize
	 */
	public $ignoreOnSerialize = true;

	/**
	 * @var bool $ignoreOnDeserialize
	 */
	public $ignoreOnDeserialize = true;
}