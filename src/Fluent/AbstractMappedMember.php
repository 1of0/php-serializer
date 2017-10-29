<?php
/**
 * Copyright (c) 2017 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Fluent;


use OneOfZero\Json\Mappers\AbstractArray\ArrayMemberMapper;

abstract class AbstractMappedMember
{
	/**
	 * @var MappedClass $parent
	 */
	private $parent;

	/**
	 * @var array $structure
	 */
	private $structure = [];

	/**
	 * @param MappedClass $parent
	 */
	public function __construct(MappedClass $parent)
	{
		$this->parent = $parent;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function name($name)
	{
		$this->setStructureValue(ArrayMemberMapper::$NAME_ATTR, $name);
		return $this;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	public function type($type)
	{
		$this->setStructureValue(ArrayMemberMapper::$TYPE_ATTR, $type);
		return $this;
	}

	/**
	 * @param bool|null $value
	 * @return $this
	 */
	public function setIgnored($value = true)
	{
		$this->setStructureValue(ArrayMemberMapper::$IGNORE_ATTR, $value);
		return $this;
	}

	/**
	 * @param bool|null $value
	 * @return $this
	 */
	public function setIncluded($value = true)
	{
		$this->setStructureValue(ArrayMemberMapper::$INCLUDE_ATTR, $value);
		return $this;
	}

	/**
	 * @param bool|null $value
	 * @return $this
	 */
	public function setIsArray($value = true)
	{
		$this->setStructureValue(ArrayMemberMapper::$ARRAY_ATTR, $value);
		return $this;
	}

	/**
	 * @param bool|null $value
	 * @return $this
	 */
	public function setIsReference($value = true)
	{
		$this->setStructureValue(ArrayMemberMapper::$REFERENCE_ATTR, $value);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setIsLazyReference()
	{
		$this->setStructureValue(ArrayMemberMapper::$REFERENCE_ATTR, 'lazy');
		return $this;
	}

	/**
	 * @param bool|null $value
	 * @return $this
	 */
	public function setSerializable($value = true)
	{
		$this->setStructureValue(ArrayMemberMapper::$SERIALIZABLE_ATTR, $value);
		return $this;
	}

	/**
	 * @param bool|null $value
	 * @return $this
	 */
	public function setDeserializable($value = true)
	{
		$this->setStructureValue(ArrayMemberMapper::$DESERIALIZABLE_ATTR, $value);
		return $this;
	}

	/**
	 * @param string $converterClass
	 * @return $this
	 */
	public function converter($converterClass)
	{
		$this->setStructureValue(ArrayMemberMapper::$CONVERTER_ATTR, $converterClass);
		return $this;
	}

	/**
	 * @param string $converterClass
	 * @return $this
	 */
	public function serializingConverter($converterClass)
	{
		if (!array_key_exists(ArrayMemberMapper::$CONVERTERS_ATTR, $this->structure))
		{
			$this->structure[ArrayMemberMapper::$CONVERTERS_ATTR] = [];
		}
		$this->structure[ArrayMemberMapper::$CONVERTERS_ATTR]['serializer'] = $converterClass;

		return $this;
	}

	/**
	 * @param string $converterClass
	 * @return $this
	 */
	public function deserializingConverter($converterClass)
	{
		if (!array_key_exists(ArrayMemberMapper::$CONVERTERS_ATTR, $this->structure))
		{
			$this->structure[ArrayMemberMapper::$CONVERTERS_ATTR] = [];
		}
		$this->structure[ArrayMemberMapper::$CONVERTERS_ATTR]['deserializer'] = $converterClass;

		return $this;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	private function setStructureValue($key, $value)
	{
		if ($value === null)
		{
			unset($this->structure[$key]);
			return;
		}
		$this->structure[$key] = $value;
	}

	/**
	 * @return MappedClass
	 */
	public function done()
	{
		return $this->parent;
	}

	/**
	 * @return array
	 */
	public function __toArray()
	{
		return $this->structure;
	}
}