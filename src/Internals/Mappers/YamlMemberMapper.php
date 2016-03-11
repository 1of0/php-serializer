<?php

namespace OneOfZero\Json\Internals\Mappers;

use OneOfZero\Json\Exceptions\SerializationException;

class YamlMemberMapper implements MemberMapperInterface
{
	use BaseMemberMapperTrait;
	use YamlMapperTrait;

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
	
	private static $includeAttributes = [
		self::INCLUDE_ATTR,
		self::NAME_ATTR,
		self::GETTER_ATTR,
		self::SETTER_ATTR,
	];

	/**
	 * @var array|null $converters
	 */
	private $converters = null;
	
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
		
		return $this->base->hasSerializingConverter();
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
		
		return $this->base->hasDeserializingConverter();
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
		
		return $this->base->getSerializingConverterType();
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
		
		return $this->base->getDeserializingConverterType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue($instance)
	{
		return $this->base->getValue($instance);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue($instance, $value)
	{
		$this->base->setValue($instance, $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		if ($this->hasAttribute(self::NAME_ATTR))
		{
			return $this->readAttribute(self::NAME_ATTR);
		}

		return $this->base->getName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		if ($this->hasAttribute(self::TYPE_ATTR))
		{
			return $this->resolveAlias($this->readAttribute(self::TYPE_ATTR));
		}
		
		return $this->base->getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isArray()
	{
		if ($this->hasAttribute(self::ARRAY_ATTR) && $this->readAttribute(self::ARRAY_ATTR))
		{
			return true;
		}
		
		return $this->base->isArray();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function isGetter()
	{
		if ($this->hasAttribute(self::GETTER_ATTR) && $this->readAttribute(self::GETTER_ATTR))
		{
			$this->validateGetterSignature();
			return true;
		}
		
		return $this->base->isGetter();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws SerializationException
	 */
	public function isSetter()
	{
		if ($this->hasAttribute(self::SETTER_ATTR) && $this->readAttribute(self::SETTER_ATTR))
		{
			$this->validateSetterSignature();
			return true;
		}
		
		return $this->base->isSetter();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReference()
	{
		if ($this->hasAttribute(self::REFERENCE_ATTR) && $this->readAttribute(self::REFERENCE_ATTR))
		{
			return true;
		}
		
		return $this->base->isReference();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isReferenceLazy()
	{
		if ($this->hasAttribute(self::REFERENCE_ATTR) && strtolower($this->readAttribute(self::REFERENCE_ATTR)) === 'lazy')
		{
			return true;
		}
		
		return $this->base->isReferenceLazy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSerializable()
	{
		if ($this->hasAttribute(self::SERIALIZABLE_ATTR) && !$this->readAttribute(self::SERIALIZABLE_ATTR))
		{
			return false;
		}
		
		return $this->base->isSerializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isDeserializable()
	{
		if ($this->hasAttribute(self::DESERIALIZABLE_ATTR) && !$this->readAttribute(self::DESERIALIZABLE_ATTR))
		{
			return false;
		}
		
		return $this->base->isDeserializable();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isIncluded()
	{
		if ($this->hasAttribute(self::IGNORE_ATTR))
		{
			return false;
		}

		foreach (self::$includeAttributes as $attribute)
		{
			if ($this->hasAttribute($attribute))
			{
				return true;
			}
		}
		
		if ($this->memberParent->wantsExplicitInclusion())
		{
			return false;
		}

		return $this->base->isIncluded();
	}

	/**
	 * @return array
	 * 
	 * @throws SerializationException
	 */
	private function getConverters()
	{
		if (!$this->hasAttribute(self::CONVERTERS_ATTR))
		{
			return [];
		}
		
		if ($this->converters !== null)
		{
			return $this->converters;
		}
		
		$converters = $this->readAttribute(self::CONVERTERS_ATTR);
		
		if (!is_array($converters))
		{
			throw new SerializationException('Invalid converter mapping');
		}

		$this->converters = [];
		
		foreach ($converters as $key => $value)
		{
			if (is_string($value))
			{
				$this->converters[$this->resolveAlias($value)] = [ 
					'serializes'    => true, 
					'deserializes'  => true 
				];
			}
			else
			{
				$this->converters[$this->resolveAlias($key)] = [ 
					'serializes'    => in_array('serializes', $value), 
					'deserializes'  => in_array('deserializes', $value) 
				];
			}
		}
		
		return $this->converters;
	}
}
