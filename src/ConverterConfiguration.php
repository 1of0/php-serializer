<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

use Carbon\Carbon;
use DateTime;
use Interop\Container\ContainerInterface;
use OneOfZero\Json\Converters\DateTimeConverter;
use OneOfZero\Json\Converters\MemberConverterInterface;
use OneOfZero\Json\Converters\ObjectConverterInterface;
use OneOfZero\Json\Helpers\ObjectHelper;
use RuntimeException;

class ConverterConfiguration
{
	private static $defaultTypeMemberConverters = [
		DateTime::class => [ DateTimeConverter::class ],
		Carbon::class => [ DateTimeConverter::class ],
	];
	
	/**
	 * @var ContainerInterface $container
	 */
	private $container;

	/**
	 * @var ObjectConverterInterface[] $globalObjectConverters
	 */
	private $globalObjectConverters = [];

	/**
	 * @var MemberConverterInterface[] $globalMemberConverters
	 */
	private $globalMemberConverters = [];

	/**
	 * @var array $typeObjectConverters
	 */
	private $typeObjectConverters = [];

	/**
	 * @var array $typeMemberConverters
	 */
	private $typeMemberConverters = [];

	/**
	 * @param ContainerInterface $container
	 * @param bool $loadDefaultConverters
	 */
	public function __construct(ContainerInterface $container = null, $loadDefaultConverters = true)
	{
		$this->container = $container;
				
		if ($loadDefaultConverters)
		{
			$this->typeMemberConverters = self::$defaultTypeMemberConverters;
		}
	}

	/**
	 * @param string|ObjectConverterInterface|MemberConverterInterface $converterClassOrInstance
	 */
	public function add($converterClassOrInstance)
	{
		$instance = ObjectHelper::getInstance($converterClassOrInstance, $this->container);
		
		if ($instance instanceof ObjectConverterInterface)
		{
			$this->addIfNotExist($instance, $this->globalObjectConverters);
		}
		elseif ($instance instanceof MemberConverterInterface)
		{
			$this->addIfNotExist($instance, $this->globalMemberConverters);
		}
		else
		{
			$class = get_class($instance);
			throw new RuntimeException("Class \"$class\" does not implement ObjectConverterInterface or MemberConverterInterface");
		}
	}

	/**
	 * @param string|ObjectConverterInterface|MemberConverterInterface $converterClassOrInstance
	 * @param string $type
	 */
	public function addForType($converterClassOrInstance, $type)
	{
		$instance = ObjectHelper::getInstance($converterClassOrInstance, $this->container);

		if ($instance instanceof ObjectConverterInterface)
		{
			if (array_key_exists($type, $this->typeObjectConverters))
			{
				$this->addIfNotExist($instance, $this->typeObjectConverters[$type]);
			}
		}
		elseif ($instance instanceof MemberConverterInterface)
		{
			if (array_key_exists($type, $this->typeMemberConverters))
			{
				$this->addIfNotExist($instance, $this->typeMemberConverters[$type]);
			}
		}
		else
		{
			$class = get_class($instance);
			throw new RuntimeException("Class \"$class\" does not implement ObjectConverterInterface or MemberConverterInterface");
		}
	}

	/**
	 * @param string|ObjectConverterInterface|MemberConverterInterface $converterClassOrInstance
	 * @param string[] $types
	 */
	public function addForTypes($converterClassOrInstance, array $types)
	{
		foreach ($types as $type)
		{
			$this->addForType($converterClassOrInstance, $type);
		}
	}

	/**
	 * @param string $class
	 */
	public function remove($class)
	{
		if (is_subclass_of($class, ObjectConverterInterface::class))
		{
			$this->globalObjectConverters = $this->removeTypeFromArray($class, $this->globalObjectConverters);
		}
		elseif (is_subclass_of($class, MemberConverterInterface::class))
		{
			$this->globalMemberConverters = $this->removeTypeFromArray($class, $this->globalMemberConverters);
		}
	}

	/**
	 * @param string $class
	 * @param string $type
	 */
	public function removeForType($class, $type)
	{
		if (is_subclass_of($class, ObjectConverterInterface::class))
		{
			if (array_key_exists($type, $this->typeObjectConverters))
			{
				$this->typeObjectConverters[$type] = $this->removeTypeFromArray($class, $this->typeObjectConverters[$type]);
			}
		}
		elseif (is_subclass_of($class, MemberConverterInterface::class))
		{
			if (array_key_exists($type, $this->typeMemberConverters))
			{
				$this->typeMemberConverters[$type] = $this->removeTypeFromArray($class, $this->typeMemberConverters[$type]);
			}
		}
	}

	/**
	 * @param string|ObjectConverterInterface|MemberConverterInterface $converterClassOrInstance
	 * @param string[] $types
	 */
	public function removeForTypes($converterClassOrInstance, array $types)
	{
		foreach ($types as $type)
		{
			$this->removeForType($converterClassOrInstance, $type);
		}
	}

	/**
	 * @param string|null $type
	 * 
	 * @return ObjectConverterInterface[]
	 */
	public function getObjectConverters($type = null)
	{
		$converters = $this->globalObjectConverters;
		
		if ($type !== null)
		{
			$converters = array_merge($converters, $this->getConvertersForType($type, $this->typeObjectConverters));
		}
		
		return $converters;
	}

	/**
	 * @param string|null $type
	 * 
	 * @return MemberConverterInterface[]
	 */
	public function getMemberConverters($type = null)
	{
		$converters = $this->globalMemberConverters;
		
		if ($type !== null)
		{
			$converters = array_merge($converters, $this->getConvertersForType($type, $this->typeMemberConverters));
		}
		
		return $converters;
	}

	/**
	 * @param string $type
	 * @param array $source
	 * 
	 * @return ObjectConverterInterface[]|MemberConverterInterface[]
	 */
	private function getConvertersForType($type, array &$source)
	{
		$mappedTypes = array_keys($source);
		
		if (in_array($type, $mappedTypes))
		{
			return $source[$type];
		}
		
		foreach (class_parents($type) as $parent)
		{
			if (in_array($parent, $mappedTypes))
			{
				return $source[$parent];
			}
		}

		foreach (class_implements($type) as $interface)
		{
			if (in_array($interface, $mappedTypes))
			{
				return $source[$interface];
			}
		}
		
		return [];
	}

	/**
	 * @param object $item
	 * @param array $array
	 */
	private function addIfNotExist($item, array &$array)
	{
		if (!in_array($item, $array))
		{
			$array[] = $item;
		}
	}

	/**
	 * @param string $type
	 * @param array $array
	 * 
	 * @return array
	 */
	private function removeTypeFromArray($type, array $array)
	{
		return array_filter($array, function($item) use ($type) {
			return get_class($item) !== $type;
		});
	}
}
