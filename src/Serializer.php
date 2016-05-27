<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

use Doctrine\Common\Cache\CacheProvider;
use Interop\Container\ContainerInterface;
use OneOfZero\Json\Helpers\Environment;
use OneOfZero\Json\Mappers\Annotation\AnnotationMapperFactory;
use OneOfZero\Json\Mappers\Caching\CachingMapperFactory;
use OneOfZero\Json\Mappers\MapperFactoryInterface;
use OneOfZero\Json\Mappers\MapperPipeline;
use OneOfZero\Json\Mappers\Reflection\ReflectionMapperFactory;
use OneOfZero\Json\Visitors\DeserializingVisitor;
use OneOfZero\Json\Visitors\SerializingVisitor;

/**
 * The serializer class provides methods to serialize and deserialize JSON data.
 */
class Serializer implements SerializerInterface
{	
	/**
	 * @var self $instance
	 */
	private static $instance;

	/**
	 * Returns a singleton instance for the Serializer class.
	 * 
	 * @return self
	 */
	public static function get()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @var ContainerInterface $container
	 */
	private $container;

	/**
	 * @var Configuration $configuration
	 */
	private $configuration;

	/**
	 * @var MapperPipeline $mapperPipeline
	 */
	private $mapperPipeline;

	/**
	 * @var ReferenceResolverInterface $referenceResolver
	 */
	private $referenceResolver;

	/**
	 * @var CacheProvider $cacheProvider
	 */
	private $cacheProvider;

	/**
	 * Initializes the Serializer class, optionally providing any of the constructor arguments as resources.
	 *
	 * @param Configuration|null $configuration
	 * @param ContainerInterface|null $container
	 * @param MapperPipeline|null $mapperPipeline
	 * @param ReferenceResolverInterface|null $referenceResolver
	 * @param CacheProvider $cacheProvider
	 */
	public function __construct(
		Configuration $configuration = null,
		ContainerInterface $container = null,
		MapperPipeline $mapperPipeline = null,
		ReferenceResolverInterface $referenceResolver = null,
		CacheProvider $cacheProvider = null
	) {
		$this->configuration = $configuration ?: new Configuration();
		$this->container = $container;
		$this->mapperPipeline = $mapperPipeline ?: $this->createDefaultPipeline();
		$this->referenceResolver = $referenceResolver;
		$this->cacheProvider = $cacheProvider;
	}

	/**
	 * {@inheritdoc}
	 */
	public function serialize($data)
	{
		$visitor = new SerializingVisitor(
			clone $this->configuration,
			$this->buildPipeline(),
			$this->container
		);

		return $this->jsonEncode($visitor->visit($data));
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize($json, $typeHint = null)
	{
		$visitor = new DeserializingVisitor(
			clone $this->configuration,
			$this->buildPipeline(),
			$this->container,
			$this->referenceResolver
		);

		return $visitor->visit($this->jsonDecode($json), null, $typeHint);
	}

	/**
	 * Casts the provided $instance into the specified $type by serializing the $instance and deserializing it into the
	 * specified $type.
	 * 
	 * @param object $instance
	 * @param string $type
	 *
	 * @return object
	 */
	public function cast($instance, $type)
	{
		return $this->deserialize($this->serialize($instance), $type);
	}

	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	private function jsonEncode($data)
	{
		$options = $this->configuration->jsonEncodeOptions;
		
		if ($this->configuration->prettyPrint)
		{
			$options |= JSON_PRETTY_PRINT;
		}
		
		return json_encode($data, $options, $this->configuration->maxDepth);
	}

	/**
	 * @param string $json
	 *
	 * @return mixed
	 */
	private function jsonDecode($json)
	{
		$options = $this->configuration->jsonEncodeOptions;
		
		return json_decode($json, false, $this->configuration->maxDepth, $options);
	}

	/**
	 * @return MapperPipeline
	 */
	private function createDefaultPipeline()
	{
		return (new MapperPipeline)
			->withFactory(new AnnotationMapperFactory(Environment::getAnnotationReader($this->container)))
			->withFactory(new ReflectionMapperFactory())
		;
	}

	/**
	 * @return MapperFactoryInterface
	 */
	private function buildPipeline()
	{
		$pipeline = $this->mapperPipeline;
		
		if ($this->cacheProvider !== null && !$pipeline->containsFactory(CachingMapperFactory::class))
		{
			$pipeline = $pipeline->withFactory(new CachingMapperFactory($this->cacheProvider));
		}
		
		return $pipeline->build(clone $this->configuration);
	}

	#region // Generic getters and setters
	// @codeCoverageIgnoreStart

	/**
	 * @return Configuration
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * @param Configuration $configuration
	 */
	public function setConfiguration(Configuration $configuration)
	{
		$this->configuration = $configuration;
	}

	/**
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * @return MapperPipeline
	 */
	public function getMapperPipeline()
	{
		return $this->mapperPipeline;
	}

	/**
	 * @param MapperPipeline $mapperPipeline
	 */
	public function setMapperPipeline(MapperPipeline $mapperPipeline)
	{
		$this->mapperPipeline = $mapperPipeline;
	}
	
	/**
	 * @return ReferenceResolverInterface
	 */
	public function getReferenceResolver()
	{
		return $this->referenceResolver;
	}

	/**
	 * @param ReferenceResolverInterface $referenceResolver
	 */
	public function setReferenceResolver(ReferenceResolverInterface $referenceResolver)
	{
		$this->referenceResolver = $referenceResolver;
	}

	/**
	 * @return CacheProvider
	 */
	public function getCacheProvider()
	{
		return $this->cacheProvider;
	}

	/**
	 * @param CacheProvider $cacheProvider
	 */
	public function setCacheProvider(CacheProvider $cacheProvider)
	{
		$this->cacheProvider = $cacheProvider;
	}

	// @codeCoverageIgnoreEnd
	#endregion
}
