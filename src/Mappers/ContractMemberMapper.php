<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

class ContractMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;

	/**
	 * @var string $serializingConverter
	 */
	private $serializingConverter;

	/**
	 * @var string $deserializingConverter
	 */
	private $deserializingConverter;

	/**
	 * @var string $name
	 */
	private $name;

	/**
	 * @var string $type
	 */
	private $type;

	/**
	 * @var bool|null $isArray
	 */
	private $isArray;

	/**
	 * @var bool|null $isGetter
	 */
	private $isGetter;

	/**
	 * @var bool|null $isSetter
	 */
	private $isSetter;

	/**
	 * @var bool|null $isReference
	 */
	private $isReference;

	/**
	 * @var bool|null $isReference
	 */
	private $isReferenceLazy;

	/**
	 * @var bool|null $isReference
	 */
	private $isSerializable;

	/**
	 * @var bool|null $isReference
	 */
	private $isDeserializable;

	/**
	 * @var bool|null $isReference
	 */
	private $isIncluded;

	#region // Getters

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		if ($this->serializingConverter !== null)
		{
			return true;
		}
		return $this->base->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		if ($this->deserializingConverter !== null)
		{
			return true;
		}
		return $this->base->hasDeserializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		if ($this->serializingConverter !== null)
		{
			return $this->serializingConverter;
		}
		return $this->base->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		if ($this->deserializingConverter !== null)
		{
			return $this->deserializingConverter;
		}
		return $this->base->getDeserializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		if ($this->name !== null)
		{
			return $this->name;
		}
		return $this->base->getName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		if ($this->type !== null)
		{
			return $this->type;
		}
		return $this->base->getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		if ($this->isArray !== null)
		{
			return $this->isArray;
		}
		return $this->base->isArray();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		if ($this->isGetter !== null)
		{
			return $this->isGetter;
		}
		return $this->base->isGetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		if ($this->isSetter !== null)
		{
			return $this->isSetter;
		}
		return $this->base->isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		if ($this->isReference !== null)
		{
			return $this->isReference;
		}
		return $this->base->isReference();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		if ($this->isReferenceLazy !== null)
		{
			return $this->isReferenceLazy;
		}
		return $this->base->isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		if ($this->isSerializable !== null)
		{
			return $this->isSerializable;
		}
		return $this->base->isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		if ($this->isDeserializable !== null)
		{
			return $this->isDeserializable;
		}
		return $this->base->isDeserializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		if ($this->isIncluded !== null)
		{
			return $this->isIncluded;
		}
		return $this->base->isIncluded();
	}

	/**
	 * @param string $serializingConverter
	 */
	public function setSerializingConverter($serializingConverter)
	{
		$this->serializingConverter = $serializingConverter;
	}

	/**
	 * @param string $deserializingConverter
	 */
	public function setDeserializingConverter($deserializingConverter)
	{
		$this->deserializingConverter = $deserializingConverter;
	}

	#endregion

	#region // Setters

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @param bool|null $isArray
	 */
	public function setIsArray($isArray)
	{
		$this->isArray = $isArray;
	}

	/**
	 * @param bool|null $isGetter
	 */
	public function setIsGetter($isGetter)
	{
		$this->isGetter = $isGetter;
	}

	/**
	 * @param bool|null $isSetter
	 */
	public function setIsSetter($isSetter)
	{
		$this->isSetter = $isSetter;
	}

	/**
	 * @param bool|null $isReference
	 */
	public function setIsReference($isReference)
	{
		$this->isReference = $isReference;
	}

	/**
	 * @param bool|null $isReferenceLazy
	 */
	public function setIsReferenceLazy($isReferenceLazy)
	{
		$this->isReferenceLazy = $isReferenceLazy;
	}

	/**
	 * @param bool|null $isSerializable
	 */
	public function setIsSerializable($isSerializable)
	{
		$this->isSerializable = $isSerializable;
	}

	/**
	 * @param bool|null $isDeserializable
	 */
	public function setIsDeserializable($isDeserializable)
	{
		$this->isDeserializable = $isDeserializable;
	}

	/**
	 * @param bool|null $isIncluded
	 */
	public function setIsIncluded($isIncluded)
	{
		$this->isIncluded = $isIncluded;
	}

	#endregion


}
