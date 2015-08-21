<?php


namespace OneOfZero\Json\DependencyInjection;


use Doctrine\Common\Annotations\AnnotationReader;
use Interop\Container\ContainerInterface;
use OneOfZero\Json\ReferenceResolverInterface;

class ContainerInteropContainerAdapter implements ContainerAdapterInterface
{
	/**
	 * @var ContainerInterface $container
	 */
	private $container;

	/**
	 * @var string $annotationReaderKey
	 */
	private $annotationReaderKey = AnnotationReader::class;

	/**
	 * @var string $referenceResolverKey
	 */
	private $referenceResolverKey = ReferenceResolverInterface::class;

	/**
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * Manually configure the key under which the AnnotationReader instance will be found in the container.
	 * By default, the class name will be used as key.
	 *
	 * @param string $key
	 */
	public function setAnnotationReaderKey($key)
	{
		$this->annotationReaderKey = $key;
	}

	/**
	 * Manually configure the key under which the ReferenceResolverInterface instance will be found in the container.
	 * By default, the class name will be used as key.
	 *
	 * @param string $key
	 */
	public function setReferenceResolverKey($key)
	{
		$this->referenceResolverKey = $key;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAnnotationReader()
	{
		return $this->container->get($this->annotationReaderKey);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getReferenceResolver()
	{
		return $this->container->get($this->referenceResolverKey);
	}
}