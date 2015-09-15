<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

class Configuration
{
	const RESOLVE_LAZY = 0;
	const RESOLVE_EAGER = 1;

	/**
	 * @var bool $prettyPrint
	 */
	public $prettyPrint = false;

	/**
	 * @var bool $includeNullValues
	 */
	public $includeNullValues = false;

	/**
	 * @var int $maxDepth
	 */
	public $maxDepth = 32;

	/**
	 * @var int $defaultResolutionType
	 */
	public $defaultResolutionType = self::RESOLVE_LAZY;
}
