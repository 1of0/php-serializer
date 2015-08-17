<?php


namespace OneOfZero\Json\Internals;


use Doctrine\Common\Annotations\AnnotationReader;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Serializer;

class SerializationContext
{
	/**
	 * @var Serializer $serializer
	 */
	public $serializer;

	/**
	 * @var Configuration $configuration
	 */
	public $configuration;

	/**
	 * @var AnnotationReader $annotationReader
	 */
	public $annotationReader;

	/**
	 * @var MemberWalker $memberWalker
	 */
	public $memberWalker;
}