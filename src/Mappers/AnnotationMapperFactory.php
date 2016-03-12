<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use Doctrine\Common\Annotations\Reader;
use OneOfZero\BetterAnnotations\Annotations;
use ReflectionClass;

class AnnotationMapperFactory implements MapperFactoryInterface
{
	use BaseFactoryTrait;
	
	/**
	 * @var Annotations $annotations
	 */
	private $annotations;

	/**
	 * @param Reader $annotationReader
	 */
	public function __construct(Reader $annotationReader)
	{
		$this->annotations = new Annotations($annotationReader);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapObject(ReflectionClass $reflector)
	{
		$mapper = new AnnotationObjectMapper($this->annotations);

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		$mapper->setBase($this->getParent()->mapObject($reflector));
		
		return $mapper;
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapMember($reflector, ObjectMapperInterface $memberParent)
	{
		$mapper = new AnnotationMemberMapper($this->annotations);

		$mapper->setFactory($this);
		$mapper->setTarget($reflector);
		$mapper->setMemberParent($memberParent);
		$mapper->setBase($this->getParent()->mapMember($reflector, $memberParent->getBase()));
		
		return $mapper;
	}
}
