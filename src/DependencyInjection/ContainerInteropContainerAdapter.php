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
	 * {@inheritdoc}
	 */
	public function get($id)
	{
		return $this->container->get($id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has($id)
	{
		return $this->container->has($id);
	}

	/**
	 * Manually configure the ID under which the AnnotationReader instance will be found in the container.
	 * By default, the class name will be used as key.
	 *
	 * @param string $id
	 */
	public function setAnnotationReaderId($id)
	{
		$this->annotationReaderKey = $id;
	}

	/**
	 * Manually configure the ID under which the ReferenceResolverInterface instance will be found in the container.
	 * By default, the class name will be used as key.
	 *
	 * @param string $id
	 */
	public function setReferenceResolverId($id)
	{
		$this->referenceResolverKey = $id;
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