<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

class PropertiesWithMultiTypeTags
{
	/**
	 * @var SimpleClass|null $nullable
	 */
	public $nullable;

	/**
	 * @var null|SimpleClass $nullBeforeClass
	 */
	public $nullBeforeClass;

	/**
	 * @var null|string|SimpleClass $threeTypes
	 */
	public $threeTypes;
}
