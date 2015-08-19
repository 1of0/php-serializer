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
	 * @var SerializationContext $serializationContext
	 */
	private $serializationContext;

	/**
	 * @param SerializationContext $context
	 */
	public function __construct(SerializationContext $context)
	{
		$this->serializationContext = $context;
	}

	public function serialize($data)
	{
		if (is_null($data))
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
	 * @param mixed $data
	 * @return array
	 */
	private function serializeMembers($data)
	{
		$class = new ReflectionClass($data);
		$context = new ClassContext($this->serializationContext, $class, $data);
		$members = $this->getMembers($context);

		$serializationData = [];

		if (!$context->hasAnnotation(NoMetadata::class))
		{
			Metadata::set($serializationData, Metadata::TYPE, $class->name);
		}

		foreach ($members as $member)
		{
			$result = $member->serialize();
			if (!is_null($result))
			{
				$serializationData[$result->propertyName] = $result->value;
			}
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
		if (is_null($deserializedData))
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

		if (is_null($typeHint))
		{
			return $deserializedData;
		}

		$class = new ReflectionClass($typeHint);
		$members = $this->getMembers(new ClassContext($this->serializationContext, $class));

		$instance = $class->newInstance();

		foreach ($members as $member)
		{
			$member->deserialize($deserializedData, $instance);
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
	 * @param ClassContext $classContext
	 * @return Member[]
	 */
	private function getMembers(ClassContext $classContext)
	{
		$members = [];

		foreach ($classContext->class->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
		{
			$members[] = new Member(
				$this->serializationContext,
				$classContext,
				new MemberContext($this->serializationContext, $property)
			);
		}

		foreach ($classContext->class->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			// Skip magic methods
			if (strpos($method->name, '__') === 0)
			{
				continue;
			}

			$members[] = new Member(
				$this->serializationContext,
				$classContext,
				new MemberContext($this->serializationContext, $method)
			);
		}

		return $members;
	}
}