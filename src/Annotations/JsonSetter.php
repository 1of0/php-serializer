<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class JsonSetter extends Annotation
{
	/**
	 * @var string $propertyName
	 */
	public $propertyName;

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
}