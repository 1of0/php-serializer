<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Helpers;

use ReflectionMethod;
use ReflectionProperty;

class ReflectionHelper
{
	/**
	 * @param ReflectionProperty|ReflectionMethod $reflector
	 * 
	 * @return bool
	 */
	public static function hasGetterSignature($reflector)
	{
		// Valid getters must have no required parameters
		return $reflector instanceof ReflectionMethod
		    && $reflector->getNumberOfRequiredParameters() === 0
		;
	}
	
	/**
	 * @param ReflectionProperty|ReflectionMethod $reflector
	 * 
	 * @return bool
	 */
	public static function hasSetterSignature($reflector)
	{
		// Valid setters must have at least one parameter, and at most one required parameter
		return $reflector instanceof ReflectionMethod
		    && $reflector->getNumberOfParameters() > 0
		    && $reflector->getNumberOfRequiredParameters() <= 1
		;
	}
	
}
