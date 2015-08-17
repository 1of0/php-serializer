<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class CustomConverter extends Annotation
{
	/**
	 * @var string $value
	 */
	public $value;

	/**
	 * @var bool $serialize
	 */
	public $serialize = true;

	/**
	 * @var bool $deserialize
	 */
	public $deserialize = true;
}