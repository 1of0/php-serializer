<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\DependencyInjection;

use Doctrine\Common\Annotations\AnnotationReader;
use OneOfZero\Json\ReferenceResolverInterface;

interface ContainerAdapterInterface
{
	/**
	 * Returns an instance for the given $id.
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function get($id);

	/**
	 * Returns whether or not the given $id is available/resolvable in the container.
	 *
	 * @param string $id
	 * @return bool
	 */
	public function has($id);

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
