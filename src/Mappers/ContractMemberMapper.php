<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use stdClass;

class ContractMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait
	{
		getValue as baseGetValue;
		setValue as baseSetValue;
	}

	/**
	 * @var string|null $name
	 */
	private $name;

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
	 * @param null|string $name
	 * @param null|string $type
	 * @param bool|null $isIncluded
	 * @param bool|null $isGetter
	 * @param bool|null $isSetter
	 * @param bool|null $isArray
	 * @param bool|null $isReference
	 * @param bool|null $isReferenceLazy
	 * @param bool|null $isSerializable
	 * @param bool|null $isDeserializable
	 * @param null|string $serializingConverter
	 * @param null|string $deserializingConverter
	 */
	public function __construct(
		$name = null,
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
		$this->name = $name;
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

	/**
	 * {@inheritdoc}
	 */
	public function getValue($instance)
	{
		if ($instance instanceof stdClass)
		{
			return $instance->{$this->getName()};
		}
		
		return $this->baseGetValue($instance);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($instance, $value)
	{
		if ($instance instanceof stdClass)
		{
			$instance->{$this->getName()} = $value;
			return;
		}

		$this->baseSetValue($instance, $value);
	}

	#region // Getters

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return ($this->name !== null) ? $this->name : $this->getBase()->getName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return ($this->type !== null) ? $this->type : $this->getBase()->getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		return ($this->isIncluded !== null) ? $this->isIncluded : $this->getBase()->isIncluded();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGetter()
	{
		return ($this->isGetter !== null) ? $this->isGetter : $this->getBase()->isGetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSetter()
	{
		return ($this->isSetter !== null) ? $this->isSetter : $this->getBase()->isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		return ($this->isArray !== null) ? $this->isArray : $this->getBase()->isArray();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		return ($this->isReference !== null) ? $this->isReference : $this->getBase()->isReference();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		return ($this->isReferenceLazy !== null) ? $this->isReferenceLazy : $this->getBase()->isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		return ($this->isSerializable !== null) ? $this->isSerializable : $this->getBase()->isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		return ($this->isDeserializable !== null) ? $this->isDeserializable : $this->getBase()->isDeserializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSerializingConverterType()
	{
		return ($this->serializingConverter !== null) ? $this->serializingConverter : $this->getBase()->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeserializingConverterType()
	{
		return ($this->deserializingConverter !== null) ? $this->deserializingConverter : $this->getBase()->getDeserializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasSerializingConverter()
	{
		return ($this->serializingConverter !== null) ? true : $this->getBase()->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasDeserializingConverter()
	{
		return ($this->deserializingConverter !== null) ? true : $this->getBase()->hasDeserializingConverter();
	}
	
	#endregion

	#region // Setters
	
	/**
	 * @param string|null $name
	 * @return self
	 */
	public function setName($name)
	{
		$this->name = $name;
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
