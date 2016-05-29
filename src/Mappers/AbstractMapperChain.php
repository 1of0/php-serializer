<?php

namespace OneOfZero\Json\Mappers;

use InvalidArgumentException;
use OneOfZero\Json\Mappers\Contract\ContractMemberMapper;
use OneOfZero\Json\Mappers\Contract\ContractObjectMapper;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

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
		$this->chain = array_fill(0, $factoryChain->getChainLength(true), null);
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
	public function getTop()
	{
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
