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
use OneOfZero\Json\ContractResolvers\ContractResolverInterface;
use OneOfZero\Json\Converters\MemberConverterInterface;
use OneOfZero\Json\Converters\ObjectConverterInterface;
use OneOfZero\Json\Exceptions\ConverterException;
use OneOfZero\Json\Exceptions\NotSupportedException;
use OneOfZero\Json\Helpers\ObjectHelper;
use OneOfZero\Json\Helpers\ProxyHelper;
use OneOfZero\Json\Mappers\FactoryChain;
use OneOfZero\Json\ReferenceResolverInterface;
use RuntimeException;

abstract class AbstractVisitor
{
	/**
	 * @var Configuration $configuration
	 */
	protected $configuration;

	/**
	 * @var FactoryChain $chain
	 */
	protected $chain;

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
	 * @var bool $hasContractResolver
	 */
	protected $hasContractResolver;

	/**
	 * @param Configuration $configuration
	 * @param FactoryChain $chain
	 * @param ContainerInterface|null $container
	 * @param ReferenceResolverInterface|null $referenceResolver
	 */
	public function __construct(
		Configuration $configuration,
		FactoryChain $chain,
		ContainerInterface $container = null,
		ReferenceResolverInterface $referenceResolver = null
	) {
		$this->configuration = $configuration;
		$this->chain = $chain;
		$this->container = $container;
		$this->referenceResolver = $referenceResolver;
		
		$this->proxyHelper = new ProxyHelper($referenceResolver);
		$this->hasContractResolver = $this->detectContractResolver();
	}

	/**
	 * @return bool
	 * 
	 * @throws NotSupportedException
	 */
	private function detectContractResolver()
	{
		if ($this->configuration->contractResolver === null)
		{
			return false;
		}
		
		if ($this->configuration->contractResolver instanceof ContractResolverInterface)
		{
			return true;
		}
		
		throw new NotSupportedException('A contract resolver must implement ContractResolverInterface');
	}

	/**
	 * @param string $mappedConverterClass
	 * @param string $objectType
	 * 
	 * @return ObjectConverterInterface[]
	 * 
	 * @throws ConverterException
	 *
	 */
	protected function getObjectConverters($mappedConverterClass, $objectType)
	{
		$converters = [];
		
		if ($mappedConverterClass !== null)
		{
			$converters[] = $this->resolveConverter($mappedConverterClass, ObjectConverterInterface::class);
		}
		
		return array_merge($converters, $this->configuration->getConverters()->getObjectConverters($objectType));
	}

	/**
	 * @param string $mappedConverterClass
	 * @param string $memberType
	 *
	 * @return MemberConverterInterface[]
	 *
	 * @throws ConverterException
	 */
	protected function getMemberConverters($mappedConverterClass, $memberType)
	{
		$converters = [];

		if ($mappedConverterClass !== null)
		{
			$converters[] = $this->resolveConverter($mappedConverterClass, MemberConverterInterface::class);
		}

		return array_merge($converters, $this->configuration->getConverters()->getMemberConverters($memberType));
	}

	/**
	 * @param string $converterClass
	 * @param string $typeConstraint
	 * 
	 * @return MemberConverterInterface|ObjectConverterInterface
	 * 
	 * @throws ConverterException
	 */
	private function resolveConverter($converterClass, $typeConstraint)
	{
		try
		{
			/** @var MemberConverterInterface|ObjectConverterInterface $converter */
			$converter = ObjectHelper::getInstance($converterClass, $this->container);
			
			if (!is_subclass_of($converter, $typeConstraint))
			{
				throw new ConverterException("Converters for objects must implement $typeConstraint");
			}
			
			return $converter;
		}
		catch (RuntimeException $e)
		{
			throw new ConverterException($e->getMessage());
		}
	}
}
