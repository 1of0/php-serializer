<?php

namespace OneOfZero\Json;

/**
 * It is not advisable to use this, as this interface likely to change or disappear with the work-in-progress
 * serialization engine.
 *
 * This interface will likely return as part of a fully functional Contract Resolver.
 *
 * @see http://www.newtonsoft.com/json/help/html/contractresolver.htm
 */
interface NameFilterInterface
{
	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function getSerializedName($name);
}