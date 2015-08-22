<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\DependencyInjection\ContainerAdapterInterface;
use OneOfZero\Json\ReferenceResolverInterface;
use OneOfZero\Json\Serializer;

class SerializerContext
{
	/**
	 * @var Serializer $serializer
	 */
	private $serializer;

	/**
	 * @var ContainerAdapterInterface $container
	 */
	private $container;

	/**
	 * @var Configuration $configuration
	 */
	private $configuration;

	/**
	 * @var AnnotationReader $annotationReader
	 */
	private $annotationReader;

	/**
	 * @var MemberWalker $memberWalker
	 */
	private $memberWalker;

	/**
	 * @return Serializer
	 */
	public function getSerializer()
	{
		return $this->serializer;
	}

	/**
	 * @param Serializer $serializer
	 */
	public function setSerializer($serializer)
	{
		$this->serializer = $serializer;
	}

	/**
	 * @param ContainerAdapterInterface $container
	 */
	public function setContainer($container)
	{
		$this->container = $container;
	}

	/**
	 * @return ContainerAdapterInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}

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
	public function setConfiguration($configuration)
	{
		$this->configuration = $configuration;
	}

	/**
	 * @return AnnotationReader
	 */
	public function getAnnotationReader()
	{
		if (!$this->annotationReader)
		{
			if ($this->container)
			{
				// Attempt to load the AnnotationReader from the container
				$this->annotationReader = $this->container->getAnnotationReader();
			}

			if (!$this->annotationReader)
			{
				// If the container didn't retrieve an AnnotationReader either, load it ourselves
				AnnotationRegistry::registerLoader(array(require __DIR__ . '/../../vendor/autoload.php', 'loadClass'));
				$this->annotationReader = new AnnotationReader();
			}
		}

		return $this->annotationReader;
	}

	/**
	 * @return ReferenceResolverInterface
	 */
	public function getReferenceResolver()
	{
		return $this->container ? $this->container->getReferenceResolver() : null;
	}

	/**
	 * @return MemberWalker
	 */
	public function getMemberWalker()
	{
		return $this->memberWalker;
	}

	/**
	 * @param MemberWalker $memberWalker
	 */
	public function setMemberWalker($memberWalker)
	{
		$this->memberWalker = $memberWalker;
	}
}