<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use ReflectionClass;
use ReflectionProperty;
use Reflector;

abstract class AbstractFactory implements FactoryInterface
{
	/**
	 * @var SourceInterface $source
	 */
	protected $source;

	/**
	 * @var array $cache
	 */
	private $cache = [];
	
	/**
	 * @param SourceInterface $source
	 */
	public function __construct(SourceInterface $source = null)
	{
		$this->source = $source;
	}
	
	public function __clone()
	{
		if ($this->source !== null)
		{
			$this->source = clone $this->source;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getObjectMapper(ReflectionClass $target, ObjectMapperChain $chain)
	{
		$cacheKey = $target->name;
		
		if (array_key_exists($cacheKey, $this->cache))
		{
			$mapper = $this->cache[$cacheKey];
		}
		else
		{
			$mapper = $this->mapObject($target, $chain);
			$this->cache[$cacheKey] = $mapper;
		}
		
		return $mapper;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMemberMapper(Reflector $target, MemberMapperChain $chain)
	{
		$memberType = $target instanceof ReflectionProperty ? 'property' : 'method';
		$memberClass = $target->getDeclaringClass()->name;
		$cacheKey = "{$memberClass}/{$memberType}/{$target->name}";
		
		if (array_key_exists($cacheKey, $this->cache))
		{
			$mapper = $this->cache[$cacheKey];
		}
		else
		{
			$mapper = $this->mapMember($target, $chain);
			$this->cache[$cacheKey] = $mapper;
		}

		return $mapper;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getSource()
	{
		return $this->source;
	}
}
