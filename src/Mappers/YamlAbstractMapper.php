<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers;

use OneOfZero\Json\Exceptions\SerializationException;

/**
 * @method YamlMapperFactory getFactory
 */
abstract class YamlAbstractMapper implements MapperInterface
{
	const NAME_ATTR = 'name';
	const TYPE_ATTR = 'type';
	const ARRAY_ATTR = 'array';
	const GETTER_ATTR = 'getter';
	const SETTER_ATTR = 'setter';
	const IGNORE_ATTR = 'ignore';
	const INCLUDE_ATTR = 'include';
	const REFERENCE_ATTR = 'reference';
	const CONVERTER_ATTR = 'converter';
	const CONVERTERS_ATTR = 'converters';
	const SERIALIZABLE_ATTR = 'serializable';
	const DESERIALIZABLE_ATTR = 'deserializable';

	/**
	 * @var array $mapping
	 */
	protected $mapping;

	/**
	 * @param array $mapping
	 */
	public function __construct(array $mapping)
	{
		$this->mapping = $mapping;
	}
	
	/**
	 * @return array
	 */
	public function getMapping()
	{
		return $this->mapping;
	}	
	
	/**
	 * @param string $attributeName
	 * 
	 * @return bool
	 */
	protected final function hasAttribute($attributeName)
	{
		return array_key_exists($attributeName, $this->mapping);
	}

	/**
	 * @param string $attributeName
	 * 
	 * @return mixed|null
	 */
	protected final function readAttribute($attributeName)
	{
		return array_key_exists($attributeName, $this->mapping) ? $this->mapping[$attributeName] : null;
	}

	/**
	 * @param string $alias
	 * 
	 * @return string
	 */
	protected final function resolveAlias($alias)
	{
		return $this->getFactory()->resolveAlias($alias);
	}

	/**
	 * @param string $class
	 * 
	 * @return string
	 */
	protected final function findAlias($class)
	{
		return $this->getFactory()->findAlias($class);
	}
	
	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function hasSerializingConverter()
	{
		if ($this->hasAttribute(self::CONVERTER_ATTR))
		{
			return true;
		}

		if ($this->hasAttribute(self::CONVERTERS_ATTR))
		{
			$converters = $this->readAttribute(self::CONVERTERS_ATTR);
			if (array_key_exists('serializer', $converters))
			{
				return true;
			}
		}

		return $this->getBase()->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function hasDeserializingConverter()
	{
		if ($this->hasAttribute(self::CONVERTER_ATTR))
		{
			return true;
		}

		if ($this->hasAttribute(self::CONVERTERS_ATTR))
		{
			$converters = $this->readAttribute(self::CONVERTERS_ATTR);
			if (array_key_exists('deserializer', $converters))
			{
				return true;
			}
		}

		return $this->getBase()->hasDeserializingConverter();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function getSerializingConverterType()
	{
		if ($this->hasAttribute(self::CONVERTER_ATTR))
		{
			return $this->resolveAlias($this->readAttribute(self::CONVERTER_ATTR));
		}

		if ($this->hasAttribute(self::CONVERTERS_ATTR))
		{
			$converters = $this->readAttribute(self::CONVERTERS_ATTR);
			if (array_key_exists('serializer', $converters))
			{
				return $this->resolveAlias($converters['serializer']);
			}
		}

		return $this->getBase()->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function getDeserializingConverterType()
	{
		if ($this->hasAttribute(self::CONVERTER_ATTR))
		{
			return $this->resolveAlias($this->readAttribute(self::CONVERTER_ATTR));
		}

		if ($this->hasAttribute(self::CONVERTERS_ATTR))
		{
			$converters = $this->readAttribute(self::CONVERTERS_ATTR);
			if (array_key_exists('deserializer', $converters))
			{
				return $this->resolveAlias($converters['deserializer']);
			}
		}

		return $this->getBase()->getDeserializingConverterType();
	}
}
