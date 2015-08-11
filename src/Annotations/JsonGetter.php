<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class JsonGetter extends Annotation
{
	/**
	 * @var string $propertyName
	 */
	public $propertyName;
}