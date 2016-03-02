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
	 *
	 * @return ArrayContext
	 */
	public function visitArray(ArrayContext $context)
	{
		foreach ($context->getArray() as $key => $value)
		{
			if ($value === null)
			{
				$context = $context->withSerializedArrayValue(null);
			}
			elseif (is_object($value))
			{
				$valueMapper = $this->mapperFactory->mapObject(new ReflectionClass($value));

				$valueContext = (new ObjectContext)
					->withReflector($valueMapper->getTarget())
					->withMapper($valueMapper)
					->withInstance($value)
				;

				$context = $context->withSerializedArrayValue($this->visitObject($valueContext));
			}
			elseif (is_array($value))
			{
				$valueContext = (new ArrayContext)
					->withArray($value)
					->withParent($context)
				;

				$context = $context->withSerializedArrayValue($this->visitArray($valueContext));
			}
			else
			{
				$context = $context->withSerializedArrayValue($value);
			}
		}

		return $context;
	}

	/**
	 * @param ObjectContext $context
	 *
	 * @return ObjectContext
	 */
	public function visitObject(ObjectContext $context)
	{
		$mapper = $context->getMapper();

		if ($context->getInstance() === null)
		{
			return null;
		}

		if (!$mapper->wantsNoMetadata())
		{
			$context = $context->withMetadata(Metadata::TYPE, $context->getReflector()->name);
		}

		if ($mapper->hasSerializingConverter())
		{
			$converter = $this->resolveObjectConverter($mapper->getSerializingConverterType());

			try
			{
				return $context->withSerializedInstance($converter->serialize($context));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		foreach ($mapper->getMembers() as $memberMapper)
		{
			$memberContext = (new MemberContext)
				->withValue($memberMapper->getValue($context->getInstance()))
				->withReflector($memberMapper->getTarget())
				->withMapper($memberMapper)
				->withParent($context)
			;

			if ($this->visitObjectMember($memberContext))
			{
				$context = $context->withSerializedMember($memberMapper->getName(), $memberContext->getSerializedValue());
			}
		}
		
		return $context;
	}

	/**
	 * @param MemberContext $context
	 *
	 * @return MemberContext|null
	 *
	 * @throws SerializationException
	 */
	private function visitObjectMember(MemberContext $context)
	{
		$mapper = $context->getMapper();

		if (!$mapper->isIncluded() || !$mapper->isSerializable())
		{
			return null;
		}

		if ($mapper->hasSerializingConverter())
		{
			$converter = $this->resolveMemberConverter($mapper->getSerializingConverterType());

			try
			{
				return $context->withSerializedValue($converter->serialize($context));
			}
			catch (ResumeSerializationException $e)
			{
			}
		}

		if ($context->getValue() !== null)
		{
			if ($mapper->isReference())
			{
				return $context->withSerializedValue($this->createReference($context));
			}

			$value = $context->getValue();
			$valueReflector = new ReflectionClass($value);

			$valueContext = (new ObjectContext)
				->withInstance($value)
				->withReflector($valueReflector)
				->withMapper($this->mapperFactory->mapObject($valueReflector))
				->withParent($context)
			;

			$this->visitObject($valueContext);
			$context = $context->withSerializedValue($valueContext->getSerializedInstance());
		}

		if (!$this->configuration->includeNullValues && $context->getSerializedValue() === null)
		{
			return null;
		}

		return $context;
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

		return $this->createReferenceItem($context, $context->getValue());
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
		$className = $context->getParent()->getReflector()->name;

		if (!is_array($context->getValue()))
		{
			throw new ReferenceException("Property $propertyName in class $className is marked as an array, but does not hold an array");
		}

		$references = [];
		foreach ($context->getValue() as $item)
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
		$className = $context->getParent()->getReflector()->name;
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
		if ($context->getMapper()->getType() === null && is_object($context->getValue()))
		{
			return get_class($context->getValue());
		}

		return null;
	}
}