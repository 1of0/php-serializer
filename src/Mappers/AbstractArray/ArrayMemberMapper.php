<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\AbstractArray;

use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Mappers\AbstractMemberMapper;

class ArrayMemberMapper extends AbstractMemberMapper
{
	use ArrayMapperTrait;
	
	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		if ($this->hasAttribute(self::$NAME_ATTR))
		{
			return $this->readAttribute(self::$NAME_ATTR);
		}

		return parent::getSerializedName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		if ($this->hasAttribute(self::$TYPE_ATTR))
		{
			return $this->resolveAlias($this->readAttribute(self::$TYPE_ATTR));
		}
		
		return parent::getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		if ($this->hasAttribute(self::$ARRAY_ATTR) && $this->readAttribute(self::$ARRAY_ATTR))
		{
			return true;
		}
		
		return parent::isArray();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function isGetter()
	{
		if ($this->hasAttribute(self::$GETTER_ATTR) && $this->readAttribute(self::$GETTER_ATTR))
		{
			$this->validateGetterSignature();
			return true;
		}
		
		return parent::isGetter();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function isSetter()
	{
		if ($this->hasAttribute(self::$SETTER_ATTR) && $this->readAttribute(self::$SETTER_ATTR))
		{
			$this->validateSetterSignature();
			return true;
		}
		
		return parent::isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		if ($this->hasAttribute(self::$REFERENCE_ATTR) && $this->readAttribute(self::$REFERENCE_ATTR))
		{
			return true;
		}
		
		return parent::isReference();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		if ($this->hasAttribute(self::$REFERENCE_ATTR) && strtolower($this->readAttribute(self::$REFERENCE_ATTR)) === 'lazy')
		{
			return true;
		}
		
		return parent::isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		if ($this->hasAttribute(self::$SERIALIZABLE_ATTR))
		{
			return (bool)$this->readAttribute(self::$SERIALIZABLE_ATTR);
		}
		
		if ($this->isClassMethod() && $this->getChain()->getTop()->isGetter())
		{
			return true;
		}
		
		return parent::isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		if ($this->hasAttribute(self::$DESERIALIZABLE_ATTR))
		{
			return (bool)$this->readAttribute(self::$DESERIALIZABLE_ATTR);
		}

		if ($this->isClassMethod() && $this->getChain()->getTop()->isGetter())
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
		if ($this->hasAttribute(self::$IGNORE_ATTR))
		{
			return false;
		}

		if ($this->getChain()->getTop()->isGetter() || $this->getChain()->getTop()->isSetter())
		{
			return true;
		}

		if ($this->hasAttribute(self::$INCLUDE_ATTR) && $this->readAttribute(self::$INCLUDE_ATTR))
		{
			return true;
		}

		if ($this->hasAttribute(self::$NAME_ATTR) && $this->readAttribute(self::$INCLUDE_ATTR) !== '')
		{
			return true;
		}
		
		if ($this->getChain()->getParent()->getTop()->isExplicitInclusionEnabled())
		{
			return false;
		}

		return parent::isIncluded();
	}
}
