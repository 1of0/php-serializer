<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use InvalidArgumentException;
use OneOfZero\Json\Mappers\Contract\ContractMemberMapper;
use OneOfZero\Json\Mappers\Contract\ContractObjectMapper;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;
use RuntimeException;

abstract class AbstractMapperChain implements MapperChainInterface
{
	/**
	 * @var FactoryChain $factoryChain
	 */
	protected $factoryChain;
	
	/**
	 * @var MapperInterface[]|ObjectMapperInterface[]|MemberMapperInterface[] $chain
	 */
	protected $chain;

	/**
	 * @var ReflectionClass|ReflectionProperty|ReflectionMethod $target
	 */
	protected $target;

	/**
	 * @param Reflector|ReflectionClass|ReflectionProperty|ReflectionMethod $target
	 * @param FactoryChain $factoryChain
	 */
	public function __construct(Reflector $target, FactoryChain $factoryChain)
	{
		$this->target = $target;
		$this->factoryChain = $factoryChain;
		$this->chain = array_fill(0, $factoryChain->getChainLength(), null);
	}
	
	/**
	 * @param int $index
	 * 
	 * @return MapperInterface|ObjectMapperInterface|MemberMapperInterface
	 */
	protected abstract function getMapper($index);
	
	/**
	 * @return MapperInterface|ObjectMapperInterface|MemberMapperInterface
	 */
	protected abstract function getNullMapper();
	
	/**
	 * {@inheritdoc}
	 */
	public function getConfiguration()
	{
		return $this->factoryChain->getConfiguration();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFactoryChain()
	{
		return $this->factoryChain;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTop($noCache = true)
	{
		if (!$noCache && $this->factoryChain->getCacheFactory() !== null)
		{
			$factory = $this->factoryChain->getCacheFactory();
			
			if ($this instanceof ObjectMapperChain)
			{
				return $factory->getObjectMapper($this->target, $this);
			}
			elseif ($this instanceof MemberMapperChain)
			{
				return $factory->getMemberMapper($this->target, $this);
			}
			
			throw new RuntimeException('Unsupported mapper chain');
		}
		
		return $this->getMapper(count($this->chain) - 1);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getNext(MapperInterface $caller)
	{
		if ($caller instanceof ContractObjectMapper || $caller instanceof ContractMemberMapper)
		{
			return $this->getTop();
		}
		
		$callerIndex = array_search($caller, $this->chain);

		if ($callerIndex === false)
		{
			throw new InvalidArgumentException('Provided caller is not in the chain');
		}

		if ($callerIndex === 0)
		{
			return $this->getNullMapper();
		}

		return $this->getMapper($callerIndex - 1);
	}
}
