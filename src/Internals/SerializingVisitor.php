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
	 * @param mixed $value
	 * @param AbstractContext|null $parent
	 *
	 * @return mixed
	 *
	 * @throws SerializationException
	 */
	public function visit($value, AbstractContext $parent = null)
	{
		if (is_object($value))
		{
			$valueMapper = $this->mapperFactory->mapObject(new ReflectionClass($value));

			$valueContext = (new ObjectContext)
				->withReflector($valueMapper->getTarget())
				->withMapper($valueMapper)
				->withInstance($value)
				->withParent($parent)
			;

			return $this->visitObject($valueContext)->getSerializedInstance();
		}

		if (is_array($value))
		{
			$valueContext = (new ArrayContext)
				->withArray($value)
				->withParent($parent)
			;

			return $this->visitArray($valueContext)->getSerializedArray();
		}

		return $value;
	}

	/**
	 * @param ArrayContext $context
	 *
	 * @return ArrayContext
	 *
	 * @throws SerializationException
	 */
	protected function visitArray(ArrayContext $context)
	{
		foreach ($context->getArray() as $key => $value)
		{
			if ($value === null)
			{
				$context = $context->withSerializedArrayValue(null, $key);
			}
			
			$context = $context->withSerializedArrayValue($this->visit($value), $key);
		}

		return $context;
	}

	/**
	 * @param ObjectContext $context
	 *
	 * @return ObjectContext|null
	 *
	 * @throws SerializationException
	 */
	protected function visitObject(ObjectContext $context)
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

			if ($result = $this->visitObjectMember($memberContext))
			{
				$context = $context->withSerializedInstanceMember($memberMapper->getName(), $result->getSerializedValue());
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
	protected function visitObjectMember(MemberContext $context)
	{
		$mapper = $context->getMapper();
		$value = $context->getValue();

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

		if ($value === null)
		{
			return $this->configuration->includeNullValues ? $context : null;
		}

		if ($mapper->isReference())
		{
			return $context->withSerializedValue($this->createReference($context));
		}

		return $context->withSerializedValue($this->visit($value));
	}

	/**
	 * @param MemberContext $context
	 *
	 * @return array|null
	 *
	 * @throws ReferenceException
	 */
	protected function createReference(MemberContext $context)
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
	protected function createReferenceArray(MemberContext $context)
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
	protected function createReferenceItem(MemberContext $context, $value)
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
	protected function getType(MemberContext $context)
	{
		if ($context->getMapper()->getType() !== null)
		{
			return $context->getMapper()->getType();
		}
		elseif (is_object($context->getValue()))
		{
			return get_class($context->getValue());
		}
		else
		{
			return null;
		}
	}
}