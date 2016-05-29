<?php

namespace OneOfZero\Json\Helpers;

use Interop\Container\ContainerInterface;
use ReflectionClass;
use RuntimeException;

class ObjectHelper
{
	/**
	 * @param string $type
	 * @param ContainerInterface|null $container
	 * @param bool $skipConstructor
	 * @return object
	 */
	public static function getInstance($type, ContainerInterface $container = null, $skipConstructor = false)
	{
		if ($type === null)
		{
			throw new RuntimeException("No type specified");
		}
		
		if ($container !== null)
		{
			if ($container->has($type))
			{
				return $container->get($type);
			}
		}

		if (class_exists($type))
		{
			$class = new ReflectionClass($type);

			if ($class->isAbstract())
			{
				throw new RuntimeException("Cannot instantiate class \"$type\", because is not a concrete type");
			}
			
			if ($skipConstructor)
			{
				return $class->newInstanceWithoutConstructor();
			}
			
			if ($class->getConstructor() !== null && $class->getConstructor()->getNumberOfRequiredParameters() > 0)
			{
				throw new RuntimeException("The constructor of class \"$type\" has more than 0 required arguments");
			}

			return $class->newInstance();
		}

		throw new RuntimeException("Cannot get instance for class \"$type\"");
	}
}
