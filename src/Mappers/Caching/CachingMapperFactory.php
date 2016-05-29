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
use Reflector;
use RuntimeException;

class CachingMapperFactory extends AbstractFactory
{
	const CACHE_NAMESPACE = '1of0_json_mapper';
	
	private static $excludedMapperMethods = [
		'getConfiguration',
		'getBase',
		'setBase',
		'getTop',
		'setTop',
		'getFactory',
		'getTarget',
		'setTarget',
		'getMembers',
		'getMemberParent',
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
		// TODO: Implement mapObject() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapMember(Reflector $target, MemberMapperChain $chain)
	{
		// TODO: Implement mapMember() method.
	}

	/*
	private function cacheObjectMapper(ObjectMapperInterface $mapper)
	{
		$mapping = [];

		foreach (self::$objectMapperMethods as $method)
		{
			$mapping[$method] = $mapper->{$method}();
		}
		
		$mapping['__members'] = $this->cacheMemberMappers($mapper->getMembers());
		
		return $mapping;
	}
	
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

			$mapping[] = $memberMapping;
		}
		
		return $mapping;
	}
	*/

	/**
	 * @return CacheProvider
	 */
	public function getCache()
	{
		return $this->cache;
	}
}

CachingMapperFactory::__constructStatic();
