<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Caching;

use Doctrine\Common\Cache\CacheProvider;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Mappers\BaseFactoryTrait;
use OneOfZero\Json\Mappers\MapperFactoryInterface;
use OneOfZero\Json\Mappers\MemberMapperInterface;
use OneOfZero\Json\Mappers\ObjectMapperInterface;
use ReflectionClass;

class CachingMapperFactory implements MapperFactoryInterface
{
	use BaseFactoryTrait;

	const CACHE_NAMESPACE = '1of0_json_mapper';
	
	const EXCLUDED_MAPPER_METHODS = [
		'getConfiguration',
		'getBase',
		'setBase',
		'getFactory',
		'setFactory',
		'getTarget',
		'setTarget',
		'getMembers',
		'getProperties',
		'getMethods',
		'setMemberParent',
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
	 * @var string $configurationHash
	 */
	private $configurationHash;

	/**
	 * @var string $pipelineHash
	 */
	private $pipelineHash;

	/**
	 * @param CacheProvider $cache
	 */
	public function __construct(CacheProvider $cache)
	{
		$this->setCache($cache);
	}

	/**
	 * @codeCoverageIgnore Not in scope
	 */
	public static function __constructStatic()
	{
		self::$objectMapperMethods = array_diff(
			get_class_methods(ObjectMapperInterface::class),
			self::EXCLUDED_MAPPER_METHODS
		);
		self::$memberMapperMethods = array_diff(
			get_class_methods(MemberMapperInterface::class),
			self::EXCLUDED_MAPPER_METHODS
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function withConfiguration(Configuration $configuration)
	{
		$this->configuration = $configuration;
		$this->configurationHash = $configuration->getHash();

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withParent(MapperFactoryInterface $parent)
	{
		$this->parent = $parent;
		
		$this->pipelineHash = '';
		
		while ($parent !== null)
		{
			$this->pipelineHash = sha1($this->pipelineHash . get_class($parent));
			$parent = $parent->getParent();
		}
		
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function mapObject(ReflectionClass $reflector)
	{
		$cacheKey = "{$this->configurationHash}_{$this->pipelineHash}_{$reflector->name}";

		$baseMapper = null;
		$mapping = $this->cache->fetch($cacheKey);

		if ($mapping === false)
		{
			$mapping = $this->cacheObjectMapper($this->parent->mapObject($reflector));
			
			$this->cache->save($cacheKey, $mapping);
		}

		$mapper = new CachedObjectMapper($mapping);
		$mapper->setFactory($this);
		$mapper->setTarget($reflector);

		if ($baseMapper !== null)
		{
			$mapper->setBase($baseMapper);
		}
		
		return $mapper;
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapMember($reflector, ObjectMapperInterface $memberParent)
	{
		return null;
	}
	
	private function cacheObjectMapper( ObjectMapperInterface $mapper)
	{
		$mapping = [];

		foreach (self::$objectMapperMethods as $method)
		{
			$mapping[$method] = $mapper->{$method}();
		}
		
		$mapping['__properties'] = $this->cacheMemberMappers($mapper->getProperties());
		$mapping['__methods'] = $this->cacheMemberMappers($mapper->getMethods());
		
		return $mapping;
	}

	/**
	 * @param MemberMapperInterface[] $mappers
	 * @return array
	 */
	private function cacheMemberMappers(array $mappers)
	{
		$mapping = [];
		
		foreach ($mappers as $mapper)
		{
			$memberMapping = [];

			foreach (self::$memberMapperMethods as $method)
			{
				$memberMapping[$method] = $mapper->{$method}();
			}

			$mapping[$mapper->getTarget()->name] = $memberMapping;
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

	/**
	 * @param CacheProvider $cache
	 */
	public function setCache(CacheProvider $cache)
	{
		$this->cache = clone $cache;
		$this->cache->setNamespace(self::CACHE_NAMESPACE);
	}
}

CachingMapperFactory::__constructStatic();
