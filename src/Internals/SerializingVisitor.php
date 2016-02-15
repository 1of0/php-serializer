<?php

namespace OneOfZero\Json\Internals;

use OneOfZero\Json\Exceptions\ReferenceException;
use OneOfZero\Json\Exceptions\ResumeSerializationException;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\ReferableInterface;
use ReflectionClass;

class SerializingVisitor extends AbstractVisitor
{
	/**
	 * @param ArrayContext $context
	 */
	public function visitArray(ArrayContext $context)
	{
		foreach ($context->array as $key => $value)
		{
			if ($value === null)
			{
				$result[$key] = null;
				continue;
			}

			$valueReflector = new ReflectionClass($value);
			$valueMapper = $this->mapperFactory->mapObject($valueReflector);
			$valueContext = new ObjectContext($value, [], $valueReflector, $valueMapper);

			$this->visitObject($valueContext);
			$context->serializedArray[$key] = $valueContext->serializedInstance;
		}
	}

	/**
	 * @param ObjectContext $context
	 */
	public function visitObject(ObjectContext $context)
	{
		$instance = $context->instance;
		$mapper = $context->getMapper();

		if ($instance === null)
		{
			return;
		}

		if (!$mapper->wantsNoMetadata())
		{
			Metadata::set($context->serializedInstance, Metadata::TYPE, $context->getReflector()->name);
		}

		if ($mapper->hasSerializingConverter())
		{
			$converter = $this->resolveObjectConverter($mapper->getSerializingConverterType());

			try
			{
				$context->serializedInstance = array_merge($context->serializedInstance, $converter->serialize($context));
				return;
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		foreach ($mapper->getMembers() as $memberMapper)
		{
			$memberMapper->getTarget()->setAccessible(true);
			$value = $memberMapper->getTarget()->getValue($instance);
			$memberContext = new MemberContext($value, [], $memberMapper->getTarget(), $memberMapper, $context);

			if ($this->visitObjectMember($memberContext))
			{
				$context->serializedInstance[$memberMapper->getName()] = $memberContext->serializedValue;
			}
		}
	}

	/**
	 * @param MemberContext $context
	 *
	 * @return bool
	 *
	 * @throws SerializationException
	 */
	private function visitObjectMember(MemberContext $context)
	{
		$mapper = $context->getMapper();

		if (!$mapper->isIncluded() || !$mapper->isSerializable())
		{
			return false;
		}

		if ($mapper->hasSerializingConverter())
		{
			$converter = $this->resolveMemberConverter($mapper->getSerializingConverterType());

			try
			{
				$context->serializedValue = $converter->serialize($context);
				return true;
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if ($context->value !== null)
		{
			if ($mapper->isReference())
			{
				$context->serializedValue = $this->createReference($context);

				return true;
			}

			$value = $context->value;
			$valueReflector = new ReflectionClass($value);
			$valueMapper = $this->mapperFactory->mapObject($valueReflector);
			$valueContext = new ObjectContext($value, [], $valueReflector, $valueMapper, $context);

			$this->visitObject($valueContext);
			$context->serializedValue = $valueContext->serializedInstance;
		}

		if ($context->serializedValue === null)
		{
			return $this->configuration->includeNullValues;
		}

		return true;
	}

	/**
	 * @param MemberContext $context
	 *
	 * @return array|null
	 *
	 * @throws ReferenceException
	 */
	private function createReference(MemberContext $context)
	{
		if ($context->getMapper()->isArray())
		{
			return $this->createReferenceArray($context);
		}

		return $this->createReferenceItem($context, $context->value);
	}

	/**
	 * @param MemberContext $context
	 *
	 * @return array
	 *
	 * @throws ReferenceException
	 */
	private function createReferenceArray(MemberContext $context)
	{
		$propertyName = $context->getReflector()->name;
		$className = $context->getParentContext()->getReflector()->name;
		$array = $context->value;

		if (!is_array($array))
		{
			throw new ReferenceException("Property $propertyName in class $className is marked as an array, but does not hold an array");
		}

		$references = [];
		foreach ($array as $item)
		{
			$references[] = $this->createReferenceItem($context, $item);
		}
		return $references;
	}

	/**
	 * @param MemberContext $context
	 * @param mixed $value
	 *
	 * @return array|null
	 *
	 * @throws ReferenceException
	 */
	private function createReferenceItem(MemberContext $context, $value)
	{
		$propertyName = $context->getReflector()->name;
		$className = $context->getParentContext()->getReflector()->name;
		$type = $this->getType($context);

		if (!($value instanceof ReferableInterface))
		{
			throw new ReferenceException("Property $propertyName in class $className is marked as a reference, but does not implement ReferableInterface");
		}

		if ($type === null)
		{
			throw new ReferenceException("Property $propertyName in class $className is marked as a reference, but does not specify or imply a valid type");
		}

		$reference = [];
		Metadata::set($reference, Metadata::TYPE, $type);
		Metadata::set($reference, Metadata::ID, $value->getId());
		return $reference;
	}

	/**
	 * @param MemberContext $context
	 *
	 * @return string|null
	 */
	private function getType(MemberContext $context)
	{
		if ($context->getMapper()->getType() === null && is_object($context->value))
		{
			return get_class($context->value);
		}

		return null;
	}
}