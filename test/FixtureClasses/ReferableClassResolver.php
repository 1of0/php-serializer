<?php


namespace OneOfZero\Json\Test\FixtureClasses;


use OneOfZero\Json\ReferableInterface;
use OneOfZero\Json\ReferenceResolverInterface;

class ReferableClassResolver implements ReferenceResolverInterface
{
	/**
	 * @param string $referenceClass
	 * @param mixed $referenceId
	 * @return ReferableInterface
	 */
	public function resolve($referenceClass, $referenceId)
	{
		if ($referenceClass === ReferableClass::class)
		{
			return new ReferableClass($referenceId);
		}
		return null;
	}
}