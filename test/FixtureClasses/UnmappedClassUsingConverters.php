<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use DateTime;

class UnmappedClassUsingConverters
{
	/**
	 * @var DateTime $privateDateObject
	 */
	private $privateDateObject;

	/**
	 * @var DateTime $dateObject
	 */
	public $dateObject;

	/**
	 * @var SimpleClass $simpleClass
	 */
	public $simpleClass;

	/**
	 * @var ReferableClass $referableClass
	 */
	public $referableClass;

	/**
	 * @var int $foo
	 */
	public $foo;

	/**
	 * @var int $bar
	 */
	public $bar;

	/**
	 * @var int $contextSensitive
	 */
	public $contextSensitive;

	/**
	 * @return DateTime
	 */
	public function getPrivateDateObject()
	{
		return $this->privateDateObject;
	}

	/**
	 * @param DateTime $dateObject
	 */
	public function setPrivateDateObject(DateTime $dateObject)
	{
		$this->privateDateObject = $dateObject;
	}
}
