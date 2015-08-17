<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD"})
 */
class Type extends Annotation
{
	/**
	 * @var string $value
	 */
	public $value;
}