<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class JsonProperty extends Annotation
{
	/**
	 * @var string $value
	 */
	public $value;

	/**
	 * @var string $class
	 */
	public $class;

	/**
	 * @var bool $isArray
	 */
	public $isArray = false;

	/**
	 * @var bool $isReference
	 */
	public $isReference = false;

	/**
	 * @var bool $serialize
	 */
	public $serialize = true;

	/**
	 * @var bool $deserialize
	 */
	public $deserialize = true;
}