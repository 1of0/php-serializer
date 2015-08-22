<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use Doctrine\Common\Annotations\AnnotationReader;
use OneOfZero\Json\DependencyInjection\ContainerAdapterInterface;
use OneOfZero\Json\ReferenceResolverInterface;

class FakeContainerAdapter implements ContainerAdapterInterface
{
	/**
	 * @var ReferenceResolverInterface $referenceResolver
	 */
	private $referenceResolver;

	public function __construct()
	{
		$this->referenceResolver = new ReferableClassResolver();
	}

	/**
	 * Returns an instance of the AnnotationReader class.
	 *
	 * @return AnnotationReader
	 */
	public function getAnnotationReader()
	{
		return null;
	}

	/**
	 * Returns an instance of the ReferenceResolverInterface interface.
	 *
	 * @return ReferenceResolverInterface
	 */
	public function getReferenceResolver()
	{
		return $this->referenceResolver;
	}

	/**
	 * Returns an instance for the given $key.
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function get($id)
	{
		return class_exists($id) ? new $id() : null;
	}

	/**
	 * Returns whether or not the given $id is available/resolvable in the container.
	 *
	 * @param string $id
	 * @return bool
	 */
	public function has($id)
	{
		return class_exists($id);
	}
}