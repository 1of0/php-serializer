<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Mappers\Null\NullMemberMapper;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

class MemberMapperChain extends AbstractMapperChain
{	
	/**
	 * @var ObjectMapperChain $parent
	 */
	protected $parent;

	/**
	 * @param Reflector|ReflectionProperty|ReflectionMethod $target
	 * @param FactoryChain $factoryChain
	 * @param ObjectMapperChain $parent
	 */
	public function __construct(Reflector $target, FactoryChain $factoryChain, ObjectMapperChain $parent)
	{
		parent::__construct($target, $factoryChain);
		
		$this->parent = $parent;
	}

	/**
	 * @return ObjectMapperChain
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getMapper($index)
	{
		if ($this->chain[$index] === null)
		{
			$factory = $this->factoryChain->getFactory($index);
			$this->chain[$index] = $factory->mapMember($this->target, $this);
		}

		return $this->chain[$index];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getNullMapper()
	{
		return new NullMemberMapper(null, $this->target, $this);
	}
}
