<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

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