<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Exceptions\SerializationException;
use ReflectionMethod;
use ReflectionProperty;

abstract class AbstractMemberMapper extends AbstractMapper implements MemberMapperInterface
{
	const GETTER_REGEX = '/^(?<prefix>get|is|has)/';
	const SETTER_REGEX = '/^(?<prefix>set)/';
	const GETTER_SETTER_REGEX = '/^(?<prefix>get|is|has|set)/';
	
	/**
	 * Returns a boolean value indicating whether or not the target field is a property.
	 *
	 * @return bool
	 */
	protected final function isClassProperty()
	{
		return $this->getTarget() instanceof ReflectionProperty;
	}

	/**
	 * Returns a boolean value indicating whether or not the target field is a method.
	 *
	 * @return bool
	 */
	protected final function isClassMethod()
	{
		return $this->getTarget() instanceof ReflectionMethod;
	}

	/**
	 * @throws SerializationException
	 */
	protected final function validateGetterSignature()
	{
		if (!($this->getTarget() instanceof ReflectionMethod))
		{
			throw new SerializationException("Field {$this->getTarget()->name} is not a method. Only methods may be marked as getters.");
		}

		$paramCount = $this->getTarget()->getNumberOfRequiredParameters();

		if ($paramCount > 0)
		{
			throw new SerializationException("Field {$this->getTarget()->name} has {$paramCount} required parameters. Fields marked as getters must have no required parameters.");
		}
	}

	/**
	 * @throws SerializationException
	 */
	protected final function validateSetterSignature()
	{
		if (!($this->getTarget() instanceof ReflectionMethod))
		{
			throw new SerializationException("Field {$this->getTarget()->name} is not a method. Only methods may be marked as setters.");
		}

		if ($this->getTarget()->getNumberOfParameters() === 0)
		{
			throw new SerializationException("Field {$this->getTarget()->name} has no parameters. Fields marked as setters must have at least one parameter.");
		}

		$paramCount = $this->getTarget()->getNumberOfRequiredParameters();

		if ($paramCount > 1)
		{
			throw new SerializationException("Field {$this->getTarget()->name} has {$paramCount} required parameters. Fields marked as setters must have one required parameter at most.");
		}
	}

	/**
	 * Determine if the method name has a prefix (get/set/is/has), and return that prefix.
	 *
	 * Returns an empty string if the method name does not have a prefix.
	 *
	 * @return string
	 */
	protected final function getMethodPrefix()
	{
		if ($this->isClassMethod() && preg_match(self::GETTER_SETTER_REGEX, $this->getTarget()->name, $matches))
		{
			return $matches['prefix'];
		}
		return '';
	}

	#region // Forwards to next mapper in chain

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializedName()
	{
		return $this->getChain()->getNext($this)->getDeserializedName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		return $this->getChain()->getNext($this)->getSerializedName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return $this->getChain()->getNext($this)->getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		return $this->getChain()->getNext($this)->isIncluded();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		return $this->getChain()->getNext($this)->isArray();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		return $this->getChain()->getNext($this)->isGetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		return $this->getChain()->getNext($this)->isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		return $this->getChain()->getNext($this)->isReference();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		return $this->getChain()->getNext($this)->isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		return $this->getChain()->getNext($this)->isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		return $this->getChain()->getNext($this)->isDeserializable();
	}
	
	#endregion
}
