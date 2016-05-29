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
use OneOfZero\Json\Mappers\AbstractMapperChain;
use OneOfZero\Json\Mappers\AbstractMemberMapper;
use OneOfZero\Json\Mappers\SourceInterface;
use OneOfZero\PhpDocReader\PhpDocReader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Reflector;

/**
 * Base implementation of a mapper that maps the serialization metadata for a property or method.
 */
class ReflectionMemberMapper extends AbstractMemberMapper
{
	/**
	 * @var PhpDocReader $docReader
	 */
	protected $docReader;

	/**
	 * @param SourceInterface $source
	 * @param Reflector|ReflectionClass|ReflectionProperty|ReflectionMethod $target
	 * @param AbstractMapperChain|null $chain
	 */
	public function __construct(
		SourceInterface $source = null,
		Reflector $target = null,
		AbstractMapperChain $chain = null
	)
	{
		parent::__construct($source, $target, $chain);

		$this->docReader = new PhpDocReader(true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		// By default assume the target member's name
		$name = $this->getTarget()->name;

		if ($this->isClassMethod())
		{
			// For methods with a prefix, trim off prefix, and make the first character is lower case
			$name = lcfirst(substr($this->getTarget()->name, strlen($this->getMethodPrefix())));
		}

		return $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		// Try determining from phpdoc (@var, @return and @param)
		
		if ($this->isClassProperty())
		{
			$type = $this->docReader->getPropertyClass($this->getTarget());
			if ($type !== null)
			{
				return $type;
			}
		}

		if ($this->getChain()->getTop()->isGetter())
		{
			$type = $this->docReader->getMethodReturnClass($this->getTarget());
			if ($type !== null)
			{
				return $type;
			}
		}

		if ($this->getChain()->getTop()->isSetter())
		{
			/** @var ReflectionParameter $setter */
			list($setter) = $this->getTarget()->getParameters();

			$type = $this->docReader->getParameterClass($setter);
			if ($type !== null)
			{
				return $type;
			}
		}
		
		// Try determining from type reflection type declarations
		
		if ($this->getChain()->getTop()->isGetter())
		{
			if (version_compare(PHP_VERSION, '7.0.0', '>='))
			{
				// If PHP 7, try using the return type declaration
				if ($this->getTarget()->getReturnType() !== null)
				{
					return $this->getTarget()->getReturnType();
				}
			}
		}

		if ($this->getChain()->getTop()->isSetter())
		{
			/** @var ReflectionParameter $setter */
			list($setter) = $this->getTarget()->getParameters();

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

		return parent::getType();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore Defers to base
	 */
	public function isArray()
	{
		return parent::isArray();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		if (!$this->isClassMethod() || !preg_match(self::GETTER_REGEX, $this->getTarget()->name))
		{
			return false;
		}

		if (!ReflectionHelper::hasGetterSignature($this->getTarget()))
		{
			return false;
		}

		$strategy = $this->getChain()->getConfiguration()->defaultMemberInclusionStrategy;

		if ($this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_GETTERS))
		{
			return true;
		}

		if (!$this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_GETTERS))
		{
			return true;
		}

		return parent::isGetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		if (!$this->isClassMethod() || !preg_match(self::SETTER_REGEX, $this->getTarget()->name))
		{
			return false;
		}

		if (!ReflectionHelper::hasSetterSignature($this->getTarget()))
		{
			return false;
		}
		
		$strategy = $this->getChain()->getConfiguration()->defaultMemberInclusionStrategy;

		if ($this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_SETTERS))
		{
			return true;
		}

		if (!$this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_SETTERS))
		{
			return true;
		}

		return parent::isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		if ($this->isClassMethod() && !$this->getChain()->getTop()->isGetter())
		{
			return false;
		}

		return parent::isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		if ($this->isClassMethod() && !$this->getChain()->getTop()->isSetter())
		{
			return false;
		}

		return parent::isDeserializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		$strategy = $this->getChain()->getConfiguration()->defaultMemberInclusionStrategy;

		if ($this->isClassProperty())
		{
			if ($this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_PROPERTIES))
			{
				return true;
			}

			if (!$this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_PROPERTIES))
			{
				return true;
			}
		}

		if ($this->getChain()->getTop()->isGetter())
		{
			if ($this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_GETTERS))
			{
				return true;
			}

			if (!$this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_GETTERS))
			{
				return true;
			}
		}

		if ($this->getChain()->getTop()->isSetter())
		{
			if ($this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::PUBLIC_SETTERS))
			{
				return true;
			}

			if (!$this->getTarget()->isPublic() && Flags::has($strategy, IncludeStrategy::NON_PUBLIC_SETTERS))
			{
				return true;
			}
		}

		return parent::isIncluded();
	}
}
