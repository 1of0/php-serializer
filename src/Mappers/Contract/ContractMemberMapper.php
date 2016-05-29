<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Contract;

use OneOfZero\Json\Mappers\AbstractMemberMapper;
use OneOfZero\Json\Mappers\MemberMapperChain;

class ContractMemberMapper extends AbstractMemberMapper
{
	/**
	 * @var string|null $deserializedName
	 */
	private $deserializedName;

	/**
	 * @var string|null $serializedName
	 */
	private $serializedName;

	/**
	 * @var string|null $type
	 */
	private $type;

	/**
	 * @var bool|null $isReference
	 */
	private $isIncluded;

	/**
	 * @var bool|null $isGetter
	 */
	private $isGetter;

	/**
	 * @var bool|null $isSetter
	 */
	private $isSetter;

	/**
	 * @var bool|null $isArray
	 */
	private $isArray;

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
	 * @var string|null $serializingConverter
	 */
	private $serializingConverter;

	/**
	 * @var string|null $deserializingConverter
	 */
	private $deserializingConverter;

	/**
	 * @param string|null $deserializedName
	 * @param string|null $serializedName
	 * @param string|null $type
	 * @param bool|null $isIncluded
	 * @param bool|null $isGetter
	 * @param bool|null $isSetter
	 * @param bool|null $isArray
	 * @param bool|null $isReference
	 * @param bool|null $isReferenceLazy
	 * @param bool|null $isSerializable
	 * @param bool|null $isDeserializable
	 * @param string|null $serializingConverter
	 * @param string|null $deserializingConverter
	 */
	public function __construct(
		$deserializedName = null,
		$serializedName = null,
		$type = null,
		$isIncluded = null,
		$isGetter = null,
		$isSetter = null,
		$isArray = null,
		$isReference = null,
		$isReferenceLazy = null,
		$isSerializable = null,
		$isDeserializable = null,
		$serializingConverter = null,
		$deserializingConverter = null
	) {
		parent::__construct();
		
		$this->deserializedName = $deserializedName;
		$this->serializedName = $serializedName;
		$this->type = $type;
		$this->isIncluded = $isIncluded;
		$this->isGetter = $isGetter;
		$this->isSetter = $isSetter;
		$this->isArray = $isArray;
		$this->isReference = $isReference;
		$this->isReferenceLazy = $isReferenceLazy;
		$this->isSerializable = $isSerializable;
		$this->isDeserializable = $isDeserializable;
		$this->serializingConverter = $serializingConverter;
		$this->deserializingConverter = $deserializingConverter;
	}

	#region // Getters

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializedName()
	{
		return ($this->deserializedName !== null) 
			? $this->deserializedName 
			: parent::getDeserializedName()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializedName()
	{
		return ($this->serializedName !== null)
			? $this->serializedName
			: parent::getSerializedName()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return ($this->type !== null)
			? $this->type
			: parent::getType()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		return ($this->isIncluded !== null)
			? $this->isIncluded
			: parent::isIncluded()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		return ($this->isGetter !== null)
			? $this->isGetter
			: parent::isGetter()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		return ($this->isSetter !== null)
			? $this->isSetter
			: parent::isSetter()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		return ($this->isArray !== null)
			? $this->isArray
			: parent::isArray()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		return ($this->isReference !== null)
			? $this->isReference
			: parent::isReference()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		return ($this->isReferenceLazy !== null)
			? $this->isReferenceLazy
			: parent::isReferenceLazy()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		return ($this->isSerializable !== null)
			? $this->isSerializable
			: parent::isSerializable()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		return ($this->isDeserializable !== null)
			? $this->isDeserializable
			: parent::isDeserializable()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return ($this->serializingConverter !== null)
			? $this->serializingConverter
			: parent::getSerializingConverterType()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		return ($this->deserializingConverter !== null)
			? $this->deserializingConverter
			: parent::getDeserializingConverterType()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return ($this->serializingConverter !== null)
			? true
			: parent::hasSerializingConverter()
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return ($this->deserializingConverter !== null)
			? true
			: parent::hasDeserializingConverter()
		;
	}
	
	#endregion

	#region // Setters
	
	/**
	 * @param string|null $serializedName
	 * @return self
	 */
	public function setSerializedName($serializedName)
	{
		$this->serializedName = $serializedName;
		return $this;
	}

	/**
	 * @param string|null $type
	 * @return self
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @param bool|null $isIncluded
	 * @return self
	 */
	public function setIsIncluded($isIncluded)
	{
		$this->isIncluded = $isIncluded;
		return $this;
	}

	/**
	 * @param bool|null $isGetter
	 * @return self
	 */
	public function setIsGetter($isGetter)
	{
		$this->isGetter = $isGetter;
		return $this;
	}

	/**
	 * @param bool|null $isSetter
	 * @return self
	 */
	public function setIsSetter($isSetter)
	{
		$this->isSetter = $isSetter;
		return $this;
	}

	/**
	 * @param bool|null $isArray
	 * @return self
	 */
	public function setIsArray($isArray)
	{
		$this->isArray = $isArray;
		return $this;
	}

	/**
	 * @param bool|null $isReference
	 * @return self
	 */
	public function setIsReference($isReference)
	{
		$this->isReference = $isReference;
		return $this;
	}

	/**
	 * @param bool|null $isReferenceLazy
	 * @return self
	 */
	public function setIsReferenceLazy($isReferenceLazy)
	{
		$this->isReferenceLazy = $isReferenceLazy;
		return $this;
	}

	/**
	 * @param bool|null $isSerializable
	 * @return self
	 */
	public function setIsSerializable($isSerializable)
	{
		$this->isSerializable = $isSerializable;
		return $this;
	}

	/**
	 * @param bool|null $isDeserializable
	 * @return self
	 */
	public function setIsDeserializable($isDeserializable)
	{
		$this->isDeserializable = $isDeserializable;
		return $this;
	}
	
	/**
	 * @param string|null $serializingConverter
	 * @return self
	 */
	public function setSerializingConverter($serializingConverter)
	{
		$this->serializingConverter = $serializingConverter;
		return $this;
	}

	/**
	 * @param string|null $deserializingConverter
	 * @return self
	 */
	public function setDeserializingConverter($deserializingConverter)
	{
		$this->deserializingConverter = $deserializingConverter;
		return $this;
	}

	#endregion
}
