<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\AbstractArray;

use OneOfZero\Json\Mappers\AbstractMapperChain;
use OneOfZero\Json\Mappers\MapperInterface;
use OneOfZero\Json\Mappers\MemberMapperChain;
use OneOfZero\Json\Mappers\ObjectMapperChain;
use OneOfZero\Json\Exceptions\SerializationException;
use ReflectionClass;

/**
 * @method ArrayAbstractSource getSource
 * @method AbstractMapperChain|ObjectMapperChain|MemberMapperChain getChain
 */
trait ArrayMapperTrait
{
	public static $NAME_ATTR = 'name';
	public static $TYPE_ATTR = 'type';
	public static $ARRAY_ATTR = 'array';
	public static $GETTER_ATTR = 'getter';
	public static $SETTER_ATTR = 'setter';
	public static $IGNORE_ATTR = 'ignore';
	public static $INCLUDE_ATTR = 'include';
	public static $REFERENCE_ATTR = 'reference';
	public static $CONVERTER_ATTR = 'converter';
	public static $CONVERTERS_ATTR = 'converters';
	public static $SERIALIZABLE_ATTR = 'serializable';
	public static $DESERIALIZABLE_ATTR = 'deserializable';

	/**
	 * @return array
	 */
	private function getMapping()
	{
		return ($this->getTarget() instanceof ReflectionClass)
			? $this->getSource()->getObjectMapping($this->getTarget())
			: $this->getSource()->getMemberMapping($this->getTarget())
		;
	}

	/**
	 * @param string $alias
	 * 
	 * @return string
	 */
	protected function resolveAlias($alias)
	{
		return $this->getSource()->resolveAlias($alias);
	}
	
	/**
	 * @param string $attributeName
	 * 
	 * @return bool
	 */
	protected final function hasAttribute($attributeName)
	{
		return array_key_exists($attributeName, $this->getMapping());
	}

	/**
	 * @param string $attributeName
	 * 
	 * @return mixed|null
	 */
	protected final function readAttribute($attributeName)
	{
		return array_key_exists($attributeName, $this->getMapping()) 
			? $this->getMapping()[$attributeName] 
			: null
		;
	}
	
	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function hasSerializingConverter()
	{
		if ($this->hasAttribute(self::$CONVERTER_ATTR))
		{
			return true;
		}

		if ($this->hasAttribute(self::$CONVERTERS_ATTR))
		{
			$converters = $this->readAttribute(self::$CONVERTERS_ATTR);
			if (array_key_exists('serializer', $converters))
			{
				return true;
			}
		}

		/** @var MapperInterface $this */
		return $this->getChain()->getNext($this)->hasSerializingConverter();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function hasDeserializingConverter()
	{
		if ($this->hasAttribute(self::$CONVERTER_ATTR))
		{
			return true;
		}

		if ($this->hasAttribute(self::$CONVERTERS_ATTR))
		{
			$converters = $this->readAttribute(self::$CONVERTERS_ATTR);
			if (array_key_exists('deserializer', $converters))
			{
				return true;
			}
		}

		/** @var MapperInterface $this */
		return $this->getChain()->getNext($this)->hasDeserializingConverter();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function getSerializingConverterType()
	{
		if ($this->hasAttribute(self::$CONVERTER_ATTR))
		{
			return $this->resolveAlias($this->readAttribute(self::$CONVERTER_ATTR));
		}

		if ($this->hasAttribute(self::$CONVERTERS_ATTR))
		{
			$converters = $this->readAttribute(self::$CONVERTERS_ATTR);
			if (array_key_exists('serializer', $converters))
			{
				return $this->resolveAlias($converters['serializer']);
			}
		}

		/** @var MapperInterface $this */
		return $this->getChain()->getNext($this)->getSerializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function getDeserializingConverterType()
	{
		if ($this->hasAttribute(self::$CONVERTER_ATTR))
		{
			return $this->resolveAlias($this->readAttribute(self::$CONVERTER_ATTR));
		}

		if ($this->hasAttribute(self::$CONVERTERS_ATTR))
		{
			$converters = $this->readAttribute(self::$CONVERTERS_ATTR);
			if (array_key_exists('deserializer', $converters))
			{
				return $this->resolveAlias($converters['deserializer']);
			}
		}
		
		/** @var MapperInterface $this */
		return $this->getChain()->getNext($this)->getDeserializingConverterType();
	}
}
