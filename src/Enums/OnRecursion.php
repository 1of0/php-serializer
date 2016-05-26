<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Enums;

/**
 * Enumeration of serializer behaviour options when recursion occurs.
 */
class OnRecursion
{
	/**
	 * Continue until the maximum recursion depth is reached.
	 */
	const CONTINUE_MAPPING = 0;

	/**
	 * Set the member null for the recursed object.
	 */
	const SET_NULL = 1;

	/**
	 * Throw recursion exception.
	 */
	const THROW_EXCEPTION = 2;

	/**
	 * Tries to create a reference for the recursed object.
	 *
	 * Will throw exception if the object does not implement ReferableInterface.
	 */
	const CREATE_REFERENCE = 3;
}
