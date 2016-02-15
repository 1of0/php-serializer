<?php

namespace OneOfZero\Json\Internals;

use Interop\Container\ContainerInterface;
use OneOfZero\Json\AbstractMemberConverter;
use OneOfZero\Json\AbstractObjectConverter;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Exceptions\ConverterException;
use OneOfZero\Json\Internals\Mappers\MapperFactoryInterface;

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
	 * @param Configuration $configuration
	 * @param MapperFactoryInterface $mapperFactory
	 * @param ContainerInterface|null $container
	 */
	public function __construct(
		Configuration $configuration,
		MapperFactoryInterface $mapperFactory,
		ContainerInterface $container = null
	)
	{
		$this->configuration = $configuration;
		$this->mapperFactory = $mapperFactory;
		$this->container = $container;
	}

	/**
	 * @param string $converterClass
	 *
	 * @return AbstractObjectConverter|null
	 *
	 * @throws ConverterException
	 */
	protected function resolveObjectConverter($converterClass)
	{
		$instance = $this->resolveConverter($converterClass);

		if ($instance instanceof AbstractObjectConverter)
		{
			return $instance;
		}

		throw new ConverterException('Converters for class members must extend the AbstractObjectConverter class');
	}

	/**
	 * @param string $converterClass
	 *
	 * @return AbstractMemberConverter|null
	 *
	 * @throws ConverterException
	 */
	protected function resolveMemberConverter($converterClass)
	{
		$instance = $this->resolveConverter($converterClass);

		if ($instance instanceof AbstractMemberConverter)
		{
			return $instance;
		}

		throw new ConverterException('Converters for class members must extend the AbstractMemberConverter class');
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

		return new $converterClass();
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