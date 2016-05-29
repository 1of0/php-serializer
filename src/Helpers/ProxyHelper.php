<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Helpers;

use OneOfZero\Json\ReferableInterface;
use OneOfZero\Json\ReferenceResolverInterface;

class ProxyHelper
{
	private static $proxyInterfaces = [
		'ProxyManager\Proxy\ProxyInterface',
		'Doctrine\Common\Persistence\Proxy'
	];

	/**
	 * @var ReferenceResolverInterface $referenceResolver
	 */
	private $referenceResolver;

	/**
	 * @param ReferenceResolverInterface $referenceResolver
	 */
	public function __construct(ReferenceResolverInterface $referenceResolver = null)
	{
		$this->referenceResolver = $referenceResolver;
	}

	/**
	 * @param string|object $classOrInstance
	 * @return bool
	 */
	public function isProxy($classOrInstance)
	{
		foreach (self::$proxyInterfaces as $proxyInterface)
		{
			if (is_subclass_of($classOrInstance, $proxyInterface))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * @param string|object $classOrInstance
	 * @return string
	 */
	public function getClassBeneath($classOrInstance)
	{
		if (!$this->isProxy($classOrInstance))
		{
			return is_string($classOrInstance) ? $classOrInstance : get_class($classOrInstance);
		}
		return $this->getClassBeneath(get_parent_class($classOrInstance));
	}

	/**
	 * @param $instance
	 * @return object
	 * 
	 * @codeCoverageIgnore Feel free to submit a PR to cover this
	 */
	public function unproxy($instance)
	{
		if (!$this->isProxy($instance))
		{
			return $instance;
		}

		if ($this->referenceResolver && $instance instanceof ReferableInterface)
		{
			return $this->referenceResolver->resolve($this->getClassBeneath($instance), $instance->getId(), false);
		}

		if (is_subclass_of($instance, 'Doctrine\Common\Persistence\Proxy'))
		{
			if (!call_user_func([ $instance, '__isInitialized' ]))
			{
				call_user_func([$instance, '__load']);
			}
		}

		if (is_subclass_of($instance, 'ProxyManager\Proxy\LazyLoadingInterface'))
		{
			if (!call_user_func([ $instance, 'isProxyInitialized' ]))
			{
				call_user_func([$instance, 'initializeProxy']);
			}
		}

		if (is_subclass_of($instance, 'ProxyManager\Proxy\ValueHolderInterface'))
		{
			return call_user_func([$instance, 'getWrappedValueHolderValue']);
		}

		return clone $instance;
	}
}
