<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Enums;

/**
 * Enumeration of serializer behaviour options when the configured maximum depth is reached.
 */
class OnMaxDepth
{
	/**
	 * Set the members null at maximal depth.
	 */
	const SET_NULL = 1;

	/**
	 * Throw recursion exception.
	 */
	const THROW_EXCEPTION = 2;
}
