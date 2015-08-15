<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\Annotations\Repository;
use OneOfZero\Json\ReferableInterface;

/**
 * @Repository(ReferableClassRepository::class)
 */
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