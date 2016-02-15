<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;

use OneOfZero\Json\Annotations\Converter;
use OneOfZero\Json\Annotations\NoMetadata;
use OneOfZero\Json\AbstractObjectConverter;
use OneOfZero\Json\Exceptions\ReferenceException;
use OneOfZero\Json\Exceptions\ResumeSerializationException;
use OneOfZero\Json\Exceptions\SerializationException;
use ReflectionClass;
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

	/**
	 * @param mixed $data
	 * @return array|mixed|null
	 * @throws ReferenceException
	 * @throws SerializationException
	 */
	public function serialize($data)
	{
		if ($data === null)
		{
			return null;
		}

		if (is_array($data) || $data instanceof stdClass)
		{
			return $this->serializeArray((array)$data);
		}

		$class = $this->getSerializingClass($data);
		$customConverter = $this->getClassCustomConverter($class, false);

		if ($customConverter)
		{
			try
			{
				$serializationData = $customConverter->serialize($data, $class->name);
				if (is_array($serializationData) && !$this->hasNoMetaDataAnnotation($class))
				{
					Metadata::set($serializationData, Metadata::TYPE, $class->name);
				}
				return $serializationData;
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if (is_object($data))
		{
			if ($this->serializationContext->getProxyHelper()->isProxy($data))
			{
				$data = $this->serializationContext->getProxyHelper()->unproxy($data);
			}

			return $this->serializeMembers($data);
		}

		return $data;
	}

	/**
	 * @param object $object
	 * @return array
	 * @throws ReferenceException
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

	/**
	 * @param mixed $deserializedData
	 * @param string|null $typeHint
	 * @return array|null|object
	 * @throws ReferenceException
	 * @throws SerializationException
	 */
	public function deserialize($deserializedData, $typeHint = null)
	{
		if ($deserializedData === null)
		{
			return null;
		}

		$class = $this->getDeserializingClass($deserializedData, $typeHint);
		$customConverter = $this->getClassCustomConverter($class, false);

		if ($customConverter)
		{
			try
			{
				return $customConverter->deserialize((array)$deserializedData, $class->name);
			}
			catch (ResumeSerializationException $e)
			{
			}
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

	/**
	 * @param mixed $deserializedData
	 * @param string|null $typeHint
	 * @return object
	 * @throws ReferenceException
	 */
	private function deserializeMembers($deserializedData, $typeHint = null)
	{
		if (!$typeHint && Metadata::contains($deserializedData, Metadata::TYPE))
		{
			$typeHint = Metadata::get($deserializedData, Metadata::TYPE);
		}

		if ($typeHint === null)
		{
			return $deserializedData;
		}

		$class = new ReflectionClass($typeHint);
		$instance = $this->serializationContext->getInstance($class);

		$members = $this->getMembers(new ReflectionContext($this->serializationContext, $class));

		foreach ($members as $member)
		{
			$member->deserialize((array)$deserializedData, $instance);
		}

		return $instance;
	}

	/**
	 * @param mixed $deserializedData
	 * @return array
	 */
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
	 * @param mixed $data
	 * @return ReflectionClass|null
	 */
	private function getSerializingClass($data)
	{
		return is_object($data) ? new ReflectionClass($data) : null;
	}

	/**
	 * @param mixed $data
	 * @param string|null $typeHint
	 * @return ReflectionClass|null
	 */
	private function getDeserializingClass($data, $typeHint)
	{
		if (!$typeHint && Metadata::contains($data, Metadata::TYPE))
		{
			$typeHint = Metadata::get($data, Metadata::TYPE);
		}

		return $typeHint ? new ReflectionClass($typeHint) : null;
	}

	private function hasNoMetaDataAnnotation(ReflectionClass $class)
	{
		return (bool)$this->serializationContext->getAnnotationReader()->getClassAnnotation($class, NoMetadata::class);
	}

	/**
	 * @param ReflectionClass $class
	 * @param bool $isSerializing
	 *
*@return null|AbstractObjectConverter
	 * @throws SerializationException
	 */
	private function getClassCustomConverter(ReflectionClass $class = null, $isSerializing = true)
	{
		/** @var Converter $annotation */
		/** @var AbstractObjectConverter $converter */

		if (!$class)
		{
			return null;
		}

		$annotationReader = $this->serializationContext->getAnnotationReader();
		$annotation = $annotationReader->getClassAnnotation($class, Converter::class);
		if ($annotation)
		{
			if (($annotation->serialize && $isSerializing) || ($annotation->deserialize && !$isSerializing))
			{
				$converter = $this->serializationContext->getInstance($annotation->value);

				if (!($converter instanceof AbstractObjectConverter))
				{
					throw new SerializationException('Converters that operate at class level must implement the CustomObjectConverterInterface interface');
				}

				if ($converter->canConvert($class->name))
				{
					return $converter;
				}
			}
		}
		return null;
	}

	/**
	 * @param ReflectionContext $classContext
	 * @return Member[]
	 */
	private function getMembers(ReflectionContext $classContext)
	{
		$members = [];

		foreach ($classContext->reflector->getProperties() as $property)
		{
			$members[] = new Member(
				$this->serializationContext,
				$classContext,
				new ReflectionContext($this->serializationContext, $property)
			);
		}

		foreach ($classContext->reflector->getMethods() as $method)
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
