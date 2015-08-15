<?php


namespace OneOfZero\Json\Test\FixtureClasses;


interface EqualityInterface
{
	/**
	 * @param EqualityInterface $object
	 * @return bool
	 */
	public function __equals($object);
}