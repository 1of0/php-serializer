<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

interface EqualityInterface
{
	/**
	 * @param EqualityInterface $object
	 * @return bool
	 */
	public function __equals($object);
}
