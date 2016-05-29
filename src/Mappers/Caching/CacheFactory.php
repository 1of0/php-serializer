<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Caching;

use Doctrine\Common\Cache\CacheProvider;
use OneOfZero\Json\Mappers\AbstractFactory;
use OneOfZero\Json\Mappers\MemberMapperChain;
use OneOfZero\Json\Mappers\MemberMapperInterface;
use OneOfZero\Json\Mappers\ObjectMapperChain;
use OneOfZero\Json\Mappers\ObjectMapperInterface;
use OneOfZero\Json\Mappers\SourceInterface;
use ReflectionClass;
use ReflectionProperty;
use Reflector;
use RuntimeException;

class CacheFactory extends AbstractFactory
{
	const CACHE_NAMESPACE = '1of0_json_mapper';
	
	private static $excludedMapperMethods = [
		'getSource',
		'getTarget',
		'setTarget',
		'getChain',
		'setChain',
		'mapMembers',
	];

	/**
	 * @var string[] $objectMapperMethods
	 */
	private static $objectMapperMethods;

	/**
	 * @var string[] $memberMapperMethods
	 */
	private static $memberMapperMethods;

	/**
	 * @var CacheProvider $cache
	 */
	private $cache;

	/**
	 * @codeCoverageIgnore Static constructors can not be covered
	 */
	public static function __constructStatic()
	{
		self::$objectMapperMethods = array_diff(
			get_class_methods(ObjectMapperInterface::class),
			self::$excludedMapperMethods
		);
		self::$memberMapperMethods = array_diff(
			get_class_methods(MemberMapperInterface::class),
			self::$excludedMapperMethods
		);
	}

	/**
	 * @param SourceInterface|null $source
	 */
	public function __construct(SourceInterface $source = null)
	{
		parent::__construct($source);
		
		if (!($source instanceof CacheSource))
		{
			throw new RuntimeException('The CacheMapperFactory requires a CacheSource instance as source');
		}
		
		$this->cache = clone $source->getCache();
		$this->cache->setNamespace(self::CACHE_NAMESPACE);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapObject(ReflectionClass $target, ObjectMapperChain $chain)
	{
		$cacheKey = "{$chain->getFactoryChain()->getHash()}_{$target->name}";

		$mapping = $this->cache->fetch($cacheKey);
		
		if ($mapping === false)
		{
			$mapping = $this->cacheObjectMapper($chain->getTop());
			
			$this->cache->save($cacheKey, $mapping);
		}
		
		return new CachedObjectMapper($mapping, $target, $chain);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapMember(Reflector $target, MemberMapperChain $chain)
	{
		$memberType = $target instanceof ReflectionProperty ? 'property' : 'method';
		$cacheKey = "{$chain->getFactoryChain()->getHash()}/{$memberType}/{$target->name}";

		$mapping = $this->cache->fetch($cacheKey);

		if ($mapping === false)
		{
			$mapping = $this->cacheMemberMapper($chain->getTop());

			$this->cache->save($cacheKey, $mapping);
		}

		return new CachedMemberMapper($mapping, $target, $chain);
	}

	/**
	 * @param ObjectMapperInterface $mapper
	 * 
	 * @return array
	 */
	private function cacheObjectMapper(ObjectMapperInterface $mapper)
	{
		$mapping = [];

		foreach (self::$objectMapperMethods as $method)
		{
			$mapping[$method] = $mapper->{$method}();
		}
		
		return $mapping;
	}

	/**
	 * @param MemberMapperInterface $mapper
	 * 
	 * @return array
	 */
	private function cacheMemberMapper(MemberMapperInterface $mapper)
	{
		$mapping = [];
		
		foreach (self::$memberMapperMethods as $method)
		{
			$mapping[$method] = $mapper->{$method}();
		}
		
		return $mapping;
	}
	
	/**
	 * @return CacheProvider
	 */
	public function getCache()
	{
		return $this->cache;
	}
}

CacheFactory::__constructStatic();
