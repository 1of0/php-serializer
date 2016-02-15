<?php

namespace OneOfZero\Json\Internals\Mappers;

use ReflectionClass;

interface MapperFactoryInterface
{
	/**
	 * @param ReflectionClass $reflector
	 *
	 * @return ObjectMapperInterface
	 */
	public function mapObject(ReflectionClass $reflector);
}