<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\RepositoryInterface;

class ReferableClassRepository implements RepositoryInterface
{
	/**
	 * @param int $id
	 * @return ReferableClass
	 */
	public static function get($id)
	{
		return new ReferableClass($id);
	}
}