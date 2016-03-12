<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\ReferableInterface;

class ReferableClass implements ReferableInterface
{
	/**
	 * @var int $id
	 */
	private $id;

	/**
	 * ReferableClass constructor.
	 * @param int $id
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getIdDouble()
	{
		return $this->id * 2;
	}
}
