<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Property extends AbstractName
{
	/**
	 * @var bool $serialize
	 */
	public $serialize = true;

	/**
	 * @var bool $deserialize
	 */
	public $deserialize = true;
}