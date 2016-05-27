<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Reflection;

use OneOfZero\Json\Enums\IncludeStrategy;
use OneOfZero\Json\Helpers\Flags;
use OneOfZero\Json\Helpers\ReflectionHelper;
use OneOfZero\Json\Mappers\BaseMemberMapperTrait;
use OneOfZero\Json\Mappers\MemberMapperInterface;
use ReflectionParameter;

/**
 * Base implementation of a mapper that maps the serialization metadata for a property or method.
 */
class ReflectionMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;
	
	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		// By default assume the target member's name
		$name = $this->target->name;

		if ($this->isClassMethod())
		{
			// For methods with a prefix, trim off prefix, and make the first character is lower case
			$name = lcfirst(substr($this->target->name, strlen($this->getMethodPrefix())));
		}

		return $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		if ($this->isGetter())
		{
			if (version_compare(PHP_VERSION, '7.0.0', '>='))
			{
				// If PHP 7, try using the return type declaration
				if ($this->target->getReturnType() !== null)
				{
					return $this->target->getReturnType();
				}
			}
		}

		if ($this->isSetter())
		{
			/** @var ReflectionParameter $setter */
			list($setter) = $this->target->getParameters();

			if (version_compare(PHP_VERSION, '7.0.0', '>='))
			{
				// If PHP 7, try using the type declaration from the first method parameter
				if ($setter->hasType())
				{
					return strval($setter->getType());
				}
			}
			
			// Try PHP 5 compatible type hint from the first method parameter
			if ($setter->getClass() !== null)
			{
				return $setter->getClass()->name;
			}
		}

		return $this->getBase()->getType();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore Defers to base
	 */
	public function isArray()
	{
		return $this->getBase()->isArray();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		if (!$this->isClassMethod() || !preg_match(self::$GETTER_REGEX, $this->target->name))
		{
			return false;
		}

		if (!ReflectionHelper::hasGetterSignature($this->target))
		{
			return false;
		}

		$strategy = $this->getConfiguration()->defaultMemberInclusionStrategy;

		if ($this->target->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_GETTERS))
		{
			return true;
		}

		if (!$this->target->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_GETTERS))
		{
			return true;
		}

		return $this->getBase()->isGetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		if (!$this->isClassMethod() || !preg_match(self::$SETTER_REGEX, $this->target->name))
		{
			return false;
		}

		if (!ReflectionHelper::hasSetterSignature($this->target))
		{
			return false;
		}
		
		$strategy = $this->getConfiguration()->defaultMemberInclusionStrategy;

		if ($this->target->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_SETTERS))
		{
			return true;
		}

		if (!$this->target->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_SETTERS))
		{
			return true;
		}

		return $this->getBase()->isSetter();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore Defers to base
	 */
	public function isReference()
	{
		return $this->getBase()->isReference();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore Defers to base
	 */
	public function isReferenceLazy()
	{
		return $this->getBase()->isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore Defers to base
	 */
	public function hasSerializingConverter()
	{
		return $this->getBase()->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore Defers to base
	 */
	public function hasDeserializingConverter()
	{
		return $this->getBase()->hasDeserializingConverter();
	}

	/**
	 * {@inheritdoc}
	 * 
	 * @codeCoverageIgnore Defers to base
	 */
	public function getSerializingConverterType()
	{
		return $this->getBase()->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore Defers to base
	 */
	public function getDeserializingConverterType()
	{
		return $this->getBase()->getDeserializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		if ($this->isClassMethod() && !$this->isGetter())
		{
			return false;
		}

		return $this->getBase()->isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		if ($this->isClassMethod() && !$this->isSetter())
		{
			return false;
		}

		return $this->getBase()->isDeserializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		$strategy = $this->getConfiguration()->defaultMemberInclusionStrategy;

		if ($this->isClassProperty())
		{
			if ($this->target->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_PROPERTIES))
			{
				return true;
			}

			if (!$this->target->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_PROPERTIES))
			{
				return true;
			}
		}

		if ($this->isGetter())
		{
			if ($this->target->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_GETTERS))
			{
				return true;
			}

			if (!$this->target->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_GETTERS))
			{
				return true;
			}
		}

		if ($this->isSetter())
		{
			if ($this->target->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_SETTERS))
			{
				return true;
			}

			if (!$this->target->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_SETTERS))
			{
				return true;
			}
		}

		return $this->getBase()->isIncluded();
	}
}
