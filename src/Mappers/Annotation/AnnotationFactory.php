<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Annotation;

use OneOfZero\Json\Mappers\AbstractFactory;
use OneOfZero\Json\Mappers\MemberMapperChain;
use OneOfZero\Json\Mappers\ObjectMapperChain;
use ReflectionClass;
use Reflector;

class AnnotationFactory extends AbstractFactory
{
	/**
	 * {@inheritdoc}
	 */
	public function mapObject(ReflectionClass $target, ObjectMapperChain $chain)
	{
		return new AnnotationObjectMapper($this->source, $target, $chain);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapMember(Reflector $target, MemberMapperChain $chain)
	{
		return new AnnotationMemberMapper($this->source, $target, $chain);
	}
}
