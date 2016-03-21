<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Enums\IncludeStrategy;
use OneOfZero\Json\Helpers\Flags;
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
	public function getValue($instance)
	{
		$this->target->setAccessible(true);

		if ($this->isClassProperty())
		{
			return $this->target->getValue($instance);
		}

		if ($this->isGetter(true))
		{
			return $this->target->invoke($instance);
		}

		return $this->getBase()->getValue($instance);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($instance, $value)
	{
		$this->target->setAccessible(true);

		if ($this->isClassProperty())
		{
			$this->target->setValue($instance, $value);
			return;
		}

		if ($this->isSetter(true))
		{
			$this->target->invoke($instance, $value);
			return;
		}

		$this->getBase()->setValue($instance, $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
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
	 */
	public function isArray()
	{
		return $this->getBase()->isArray();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param bool $testSignatureOnly
	 */
	public function isGetter($testSignatureOnly = false)
	{
		if (!$this->isClassMethod() || !preg_match(self::$GETTER_REGEX, $this->target->name))
		{
			return false;
		}

		if ($this->target->getNumberOfRequiredParameters() > 0)
		{
			// Valid getters must have no required parameters
			return false;
		}

		if ($testSignatureOnly)
		{
			return true;
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
	 *
	 * @param bool $testSignatureOnly
	 */
	public function isSetter($testSignatureOnly = false)
	{
		if (!$this->isClassMethod() || !preg_match(self::$SETTER_REGEX, $this->target->name))
		{
			return false;
		}

		if ($this->target->getNumberOfParameters() === 0 || $this->target->getNumberOfRequiredParameters() > 1)
		{
			// Valid setters must have at least one parameter, and at most one required parameter
			return false;
		}

		if ($testSignatureOnly)
		{
			return true;
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
	 */
	public function isReference()
	{
		return $this->getBase()->isReference();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		return $this->getBase()->isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return $this->getBase()->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return $this->getBase()->hasDeserializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return $this->getBase()->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
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
