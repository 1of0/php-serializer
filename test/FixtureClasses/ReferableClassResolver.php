<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\ReferableInterface;
use OneOfZero\Json\ReferenceResolverInterface;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

class ReferableClassResolver implements ReferenceResolverInterface
{
	/**
	 * @var LazyLoadingValueHolderFactory $lazyFactory
	 */
	private $lazyFactory;

	/**
	 * @var AccessInterceptorValueHolderFactory $lazyFactory
	 */
	private $interceptorFactory;

	/**
	 * 
	 */
	public function __construct()
	{
		$this->lazyFactory = new LazyLoadingValueHolderFactory();
		$this->interceptorFactory = new AccessInterceptorValueHolderFactory();
	}
	
	/**
	 * @param string $referenceClass
	 * @param mixed $referenceId
	 * @param bool $lazy
	 * @return ReferableInterface
	 */
	public function resolve($referenceClass, $referenceId, $lazy = true)
	{
		if ($referenceClass !== ReferableClass::class)
		{
			return null;
		}

		if ($lazy)
		{
			/** @var ReferableClassResolver $referenceResolver */
			$referenceResolver = $this;

			$lazyProxy = $this->lazyFactory->createProxy($referenceClass,
				function (&$wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, &$initializer)
				use ($referenceResolver, $referenceClass, $referenceId)
				{
					$initializer = null;
					$wrappedObject = $referenceResolver->resolve($referenceClass, $referenceId, false);
					return true;
				}
			);
			$proxy = $this->interceptorFactory->createProxy($lazyProxy, [
				'getId' => function ($proxy, $instance, $method, $params, &$returnEarly) use ($referenceId)
				{
					$returnEarly = true;
					return $referenceId;
				}
			]);

			return $proxy;
		}
		else
		{
			return new ReferableClass($referenceId);
		}
	}
}
