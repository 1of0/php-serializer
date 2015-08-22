<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;

use OneOfZero\Json\Annotations\NoMetadata;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use stdClass;

class MemberWalker
{
	/**
	 * @var SerializerContext $serializationContext
	 */
	private $serializationContext;

	/**
	 * @param SerializerContext $context
	 */
	public function __construct(SerializerContext $context)
	{
		$this->serializationContext = $context;
	}

	public function serialize($data)
	{
		if ($data === null)
		{
			return null;
		}

		if (is_array($data) || $data instanceof stdClass)
		{
			return $this->serializeArray($data);
		}

		if (is_object($data))
		{
			return $this->serializeMembers($data);
		}

		return $data;
	}

	/**
	 * @param object $object
	 * @return array
	 */
	private function serializeMembers($object)
	{
		$class = new ReflectionClass($object);
		$context = new ReflectionContext($this->serializationContext, $class);
		$members = $this->getMembers($context);

		$serializationData = [];

		if (!$context->hasAnnotation(NoMetadata::class))
		{
			Metadata::set($serializationData, Metadata::TYPE, $class->name);
		}

		foreach ($members as $member)
		{
			$member->serialize($object, $serializationData);
		}

		return $serializationData;
	}

	/**
	 * @param array $array
	 * @return array
	 */
	private function serializeArray(array $array)
	{
		$result = [];
		foreach ($array as $key => $item)
		{
			$result[$key] = $this->serialize($item);
		}
		return $result;
	}

	public function deserialize($deserializedData, $typeHint = null)
	{
		if ($deserializedData === null)
		{
			return null;
		}

		if (is_array($deserializedData))
		{
			return $this->deserializeArray($deserializedData);
		}

		if (is_object($deserializedData))
		{
			return $this->deserializeMembers($deserializedData, $typeHint);
		}

		return $deserializedData;
	}

	private function deserializeMembers($deserializedData, $typeHint = null)
	{
		if (Metadata::contains($deserializedData, Metadata::TYPE))
		{
			$typeHint = Metadata::get($deserializedData, Metadata::TYPE);
		}

		if ($typeHint === null)
		{
			return $deserializedData;
		}

		$class = new ReflectionClass($typeHint);
		$members = $this->getMembers(new ReflectionContext($this->serializationContext, $class));

		$instance = $class->newInstance();

		foreach ($members as $member)
		{
			$member->deserialize((array)$deserializedData, $instance);
		}

		return $instance;
	}

	private function deserializeArray($deserializedData)
	{
		$result = [];
		foreach ($deserializedData as $key => $item)
		{
			$result[$key] = $this->deserialize($item);
		}
		return $result;
	}

	/**
	 * @param ReflectionContext $classContext
	 * @return Member[]
	 */
	private function getMembers(ReflectionContext $classContext)
	{
		$members = [];

		foreach ($classContext->reflector->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
		{
			$members[] = new Member(
				$this->serializationContext,
				$classContext,
				new ReflectionContext($this->serializationContext, $property)
			);
		}

		foreach ($classContext->reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			// Skip magic methods
			if (strpos($method->name, '__') === 0)
			{
				continue;
			}

			$members[] = new Member(
				$this->serializationContext,
				$classContext,
				new ReflectionContext($this->serializationContext, $method)
			);
		}

		return $members;
	}
}