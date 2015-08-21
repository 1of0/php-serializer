<?php


namespace OneOfZero\Json\DependencyInjection;


use Doctrine\Common\Annotations\AnnotationReader;
use OneOfZero\Json\ReferenceResolverInterface;

interface ContainerAdapterInterface
{
	/**
	 * Returns an instance of the AnnotationReader class.
	 *
	 * @return AnnotationReader
	 */
	public function getAnnotationReader();

	/**
	 * Returns an instance of the ReferenceResolverInterface interface.
	 *
	 * @return ReferenceResolverInterface
	 */
	public function getReferenceResolver();
}