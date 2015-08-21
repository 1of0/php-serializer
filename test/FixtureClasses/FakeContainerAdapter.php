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
}