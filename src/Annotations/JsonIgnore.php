<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class JsonIgnore extends Annotation
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