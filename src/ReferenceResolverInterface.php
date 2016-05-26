<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

/**
 * Defines an interface for a reference resolver or repository that turns references created from a ReferableInterface 
 * instance into an instance again.
 */
interface ReferenceResolverInterface
{
	/**
	 * Should resolve an instance of the provided class going by the provided reference ID. 
	 * 
	 * If the lazy argument is set to true, a proxy should be returned instead of the actual instance.
	 * 
	 * @param string $referenceClass
	 * @param mixed $referenceId
	 * @param bool $lazy
	 * 
	 * @return ReferableInterface
	 */
	public function resolve($referenceClass, $referenceId, $lazy);
}
