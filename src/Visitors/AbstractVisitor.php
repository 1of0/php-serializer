<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Visitors;

use Interop\Container\ContainerInterface;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Converters\AbstractMemberConverter;
use OneOfZero\Json\Converters\AbstractObjectConverter;
use OneOfZero\Json\Converters\MemberConverterInterface;
use OneOfZero\Json\Converters\ObjectConverterInterface;
use OneOfZero\Json\Exceptions\ConverterException;
use OneOfZero\Json\Helpers\ProxyHelper;
use OneOfZero\Json\Mappers\MapperFactoryInterface;
use OneOfZero\Json\ReferenceResolverInterface;
use ReflectionClass;

abstract class AbstractVisitor
{
	/**
	 * @var Configuration $configuration
	 */
	protected $configuration;

	/**
	 * @var MapperFactoryInterface $mapperFactory
	 */
	protected $mapperFactory;

	/**
	 * @var ContainerInterface $container
	 */
	protected $container;

	/**
	 * @var ReferenceResolverInterface $referenceResolver
	 */
	protected $referenceResolver;

	/**
	 * @var ProxyHelper $proxyHelper
	 */
	protected $proxyHelper;

	/**
	 * @param Configuration $configuration
	 * @param MapperFactoryInterface $mapperFactory
	 * @param ContainerInterface|null $container
	 * @param ReferenceResolverInterface|null $referenceResolver
	 */
	public function __construct(
		Configuration $configuration,
		MapperFactoryInterface $mapperFactory,
		ContainerInterface $container = null,
		ReferenceResolverInterface $referenceResolver = null
	) {
		$this->configuration = $configuration;
		$this->mapperFactory = $mapperFactory;
		$this->container = $container;
		$this->referenceResolver = $referenceResolver;
		$this->proxyHelper = new ProxyHelper($referenceResolver);
	}

	/**
	 * @param string $converterClass
	 *
	 * @return ObjectConverterInterface|null
	 *
	 * @throws ConverterException
	 */
	protected function resolveObjectConverter($converterClass)
	{
		$instance = $this->resolveConverter($converterClass);

		if ($instance instanceof ObjectConverterInterface)
		{
			return $instance;
		}

		throw new ConverterException('Converters for objects must implement ObjectConverterInterface');
	}

	/**
	 * @param string $converterClass
	 *
	 * @return MemberConverterInterface|null
	 *
	 * @throws ConverterException
	 */
	protected function resolveMemberConverter($converterClass)
	{
		$instance = $this->resolveConverter($converterClass);

		if ($instance instanceof MemberConverterInterface)
		{
			return $instance;
		}

		throw new ConverterException('Converters for class members must implement MemberConverterInterface');
	}

	/**
	 * @param string $converterClass
	 *
	 * @return mixed
	 *
	 * @throws ConverterException
	 */
	private function resolveConverter($converterClass)
	{
		if ($converterClass === null)
		{
			throw new ConverterException("No converter type specified");
		}

		if (!class_exists($converterClass) && !$this->containerHas($converterClass))
		{
			throw new ConverterException("Cannot resolve converter of type \"$converterClass\"");
		}

		if ($this->containerHas($converterClass))
		{
			return $this->containerGet($converterClass);
		}
		
		$reflector = new ReflectionClass($converterClass);
		
		return $reflector->newInstanceWithoutConstructor();
	}

	/**
	 * @param string $id
	 *
	 * @return mixed|null
	 */
	protected function containerGet($id)
	{
		if (!$this->containerHas($id))
		{
			return null;
		}
		return $this->container->get($id);
	}

	/**
	 * @param string $id
	 *
	 * @return bool
	 */
	protected function containerHas($id)
	{
		return $this->container !== null && $this->container->has($id);
	}
}
