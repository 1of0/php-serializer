<?php
/**
 * Copyright (c) 2017 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Fluent;


use OneOfZero\Json\Mappers\AbstractArray\ArrayObjectMapper;

class MappedClass
{
	/**
	 * @var Mapping $parent
	 */
	private $parent;

	/**
	 * @var array $structure
	 */
	private $structure = [];

	/**
	 * @var AbstractMappedMember[] $members
	 */
	private $members = [];

	/**
	 * @param Mapping $parent
	 */
	public function __construct(Mapping $parent)
	{
		$this->parent = $parent;
	}

	/**
	 * @param string $name
	 * @return MappedProperty
	 */
	public function property($name)
	{
		$property = new MappedProperty($this);
		$this->members[$name] = $property;
		return $property;
	}

	/**
	 * @param string $name
	 * @return MappedGetter
	 */
	public function getter($name)
	{
		$getter = new MappedGetter($this);
		$this->members[$name] = $getter;
		return $getter;
	}

	/**
	 * @param string $name
	 * @return MappedSetter
	 */
	public function setter($name)
	{
		$setter = new MappedSetter($this);
		$this->members[$name] = $setter;
		return $setter;
	}

	/**
	 * @param bool $value
	 * @return $this
	 */
	public function setExplicitInclusion($value = true)
	{
		$this->setStructureValue(ArrayObjectMapper::$EXPLICIT_INCLUSION_ATTR, $value);
		return $this;
	}

	/**
	 * @param bool $value
	 * @return $this
	 */
	public function setNoMetadata($value = true)
	{
		if (is_bool($value))
		{
			$value = !$value;
		}
		$this->setStructureValue(ArrayObjectMapper::$METADATA_ATTR, $value);
		return $this;
	}

	/**
	 * @param string $converterClass
	 * @return $this
	 */
	public function converter($converterClass)
	{
		$this->setStructureValue(ArrayObjectMapper::$CONVERTER_ATTR, $converterClass);
		return $this;
	}

	/**
	 * @param string $converterClass
	 * @return $this
	 */
	public function serializingConverter($converterClass)
	{
		if (!array_key_exists(ArrayObjectMapper::$CONVERTERS_ATTR, $this->structure))
		{
			$this->structure[ArrayObjectMapper::$CONVERTERS_ATTR] = [];
		}
		$this->structure[ArrayObjectMapper::$CONVERTERS_ATTR]['serializer'] = $converterClass;

		return $this;
	}

	/**
	 * @param string $converterClass
	 * @return $this
	 */
	public function deserializingConverter($converterClass)
	{
		if (!array_key_exists(ArrayObjectMapper::$CONVERTERS_ATTR, $this->structure))
		{
			$this->structure[ArrayObjectMapper::$CONVERTERS_ATTR] = [];
		}
		$this->structure[ArrayObjectMapper::$CONVERTERS_ATTR]['deserializer'] = $converterClass;

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
	 * @return Mapping
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
		$structure = $this->structure;
		$structure['properties'] = [];
		$structure['methods'] = [];

		foreach ($this->members as $memberName => $member)
		{
			if ($member instanceof MappedProperty)
			{
				$structure['properties'][$memberName] = $member->__toArray();
			}
			else
			{
				$structure['methods'][$memberName] = $member->__toArray();
			}
		}

		return $structure;
	}
}