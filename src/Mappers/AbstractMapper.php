<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Mappers\AbstractArray\ArrayAbstractSource;
use OneOfZero\Json\Mappers\Annotation\AnnotationSource;
use OneOfZero\Json\Mappers\File\FileSource;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

abstract class AbstractMapper implements MapperInterface
{
	/**
	 * @var SourceInterface|ArrayAbstractSource|FileSource|AnnotationSource $source
	 */
	private $source;
	
	/**
	 * @var ReflectionClass|ReflectionProperty|ReflectionMethod $target
	 */
	private $target;

	/**
	 * @var AbstractMapperChain $chain
	 */
	private $chain;

	/**
	 * @param SourceInterface $source
	 * @param Reflector|ReflectionClass|ReflectionProperty|ReflectionMethod $target
	 * @param AbstractMapperChain|null $chain
	 */
	public function __construct(
		SourceInterface $source = null,
		Reflector $target = null,
		AbstractMapperChain $chain = null
	)
	{
		$this->source = $source;
		$this->target = $target;
		$this->chain = $chain;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getSource()
	{
		return $this->source;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getTarget()
	{
		return $this->target;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function setTarget(Reflector $target)
	{
		$this->target = $target;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function getChain()
	{
		return $this->chain;
	}

	/**
	 * {@inheritdoc}
	 */
	public final function setChain(MapperChainInterface $chain)
	{
		$this->chain = $chain;
	}

	#region // Forwards to next mapper in chain

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		$this->getChain()->getNext($this)->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		$this->getChain()->getNext($this)->getDeserializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		$this->getChain()->getNext($this)->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		$this->getChain()->getNext($this)->hasDeserializingConverter();
	}
	
	#endregion
}
