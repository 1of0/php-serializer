<?php


namespace OneOfZero\Json\Annotations;


use Doctrine\Common\Annotations\Annotation;

abstract class AbstractName extends Annotation
{
	/**
	 * @var string $name
	 */
	public $name;
}