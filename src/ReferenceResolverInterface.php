<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

interface ReferenceResolverInterface
{
	/**
	 * @param string $referenceClass
	 * @param mixed $referenceId
	 * @param bool $lazy
	 * @return ReferableInterface
	 */
	public function resolve($referenceClass, $referenceId, $lazy = true);
}
