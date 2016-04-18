<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

use Interop\Container\ContainerInterface;
use OneOfZero\Json\Helpers\Environment;
use OneOfZero\Json\Mappers\AnnotationMapperFactory;
use OneOfZero\Json\Mappers\MapperFactoryInterface;
use OneOfZero\Json\Mappers\MapperPipeline;
use OneOfZero\Json\Mappers\ReflectionMapperFactory;
use OneOfZero\Json\Visitors\DeserializingVisitor;
use OneOfZero\Json\Visitors\SerializingVisitor;

class Serializer implements SerializerInterface
{	
	/**
	 * @var Serializer $instance
	 */
	private static $instance;

	/**
	 * @return Serializer
	 */
	public static function get()
	{
		if (!self::$instance)
		{
			self::$instance = new Serializer();
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
	 * @var MapperFactoryInterface $mapperFactory
	 */
	private $mapperFactory;

	/**
	 * @var ReferenceResolverInterface $referenceResolver
	 */
	private $referenceResolver;

	/**
	 * @param Configuration|null $configuration
	 * @param ContainerInterface|null $container
	 * @param MapperFactoryInterface|null $mapperFactory
	 * @param ReferenceResolverInterface|null $referenceResolver
	 */
	public function __construct(
		Configuration $configuration = null,
		ContainerInterface $container = null,
		MapperFactoryInterface $mapperFactory = null,
		ReferenceResolverInterface $referenceResolver = null
	) {
		$this->configuration = $configuration ?: new Configuration();
		$this->container = $container;
		$this->mapperFactory = $mapperFactory ?: $this->createDefaultPipeline();
		$this->referenceResolver = $referenceResolver;
	}

	/**
	 * {@inheritdoc}
	 */
	public function serialize($data)
	{
		$visitor = new SerializingVisitor(
			clone $this->configuration,
			$this->mapperFactory->withConfiguration(clone $this->configuration),
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
			$this->mapperFactory->withConfiguration(clone $this->configuration),
			$this->container,
			$this->referenceResolver
		);

		return $visitor->visit($this->jsonDecode($json), null, $typeHint);
	}

	/**
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
	 * @return MapperFactoryInterface
	 */
	private function createDefaultPipeline()
	{
		return (new MapperPipeline)
			->addFactory(new AnnotationMapperFactory(Environment::getAnnotationReader($this->container)))
			->addFactory(new ReflectionMapperFactory())
			->build()
		;
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
	 * @return MapperFactoryInterface
	 */
	public function getMapperFactory()
	{
		return $this->mapperFactory;
	}

	/**
	 * @param MapperFactoryInterface $mapperFactory
	 */
	public function setMapperFactory(MapperFactoryInterface $mapperFactory)
	{
		$this->mapperFactory = $mapperFactory;
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

	// @codeCoverageIgnoreEnd
	#endregion
}
