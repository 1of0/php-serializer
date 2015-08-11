<?php


namespace OneOfZero\Json\Internals;


use Doctrine\Common\Annotations\Annotation;

class Member
{
	const TYPE_PROPERTY = 0;
	const TYPE_METHOD = 1;

	const VALUE_IS_SERIALIZED = 0;
	const VALUE_IS_DESERIALIZED = 1;
	const VALUE_IS_NULL = 2;
	const VALUE_IS_OBJECT = 3;
	const VALUE_IS_ARRAY = 4;
	const VALUE_IS_VALUE_TYPE = 5;

	/**
	 * @var object $instance
	 */
	public $instance;

	/**
	 * @var string $name
	 */
	public $name;

	/**
	 * @var int $type
	 */
	public $type;

	/**
	 * @var Annotation[] $annotations
	 */
	public $annotations;

	/**
	 * @var string $propertyName
	 */
	public $propertyName;

	/**
	 * @var mixed $value
	 */
	public $value;

	/**
	 * @var array $serializationData
	 */
	public $serializationData;

	/**
	 * Member constructor.
	 * @param object $instance
	 * @param string $name
	 * @param int $type
	 * @param Annotation[] $memberAnnotations
	 */
	public function __construct($instance, $name, $type, array $memberAnnotations)
	{
		$this->instance = $instance;
		$this->name = $name;
		$this->type = $type;
		$this->annotations = $memberAnnotations;
	}

	/**
	 * @param null|string $annotationClass
	 * @return Annotation[]
	 */
	public function getAnnotations($annotationClass = null)
	{
		if (is_null($annotationClass))
		{
			return $this->annotations;
		}

		$annotations = [];
		foreach ($this->annotations as $annotation)
		{
			if (get_class($annotation) === $annotationClass)
			{
				$annotations[] = $annotation;
			}
		}
		return $annotations;
	}

	/**
	 * @param string $annotationClass
	 * @return Annotation|null
	 */
	public function getAnnotation($annotationClass)
	{
		foreach ($this->annotations as $annotation)
		{
			if (get_class($annotation) === $annotationClass)
			{
				return $annotation;
			}
		}
		return null;
	}

	/**
	 * @return string
	 */
	public function getPropertyName()
	{
		return is_null($this->propertyName) ? $this->name : $this->propertyName;
	}

	/**
	 * @return bool
	 */
	public function isArray()
	{
		return is_array($this->value);
	}

	/**
	 * @return bool
	 */
	public function isValueType()
	{
		return !is_object($this->value) && !is_array($this->value);
	}

	/**
	 * @return bool
	 */
	public function isObject()
	{
		return is_object($this->value);
	}

	/**
	 * @return null|string
	 */
	public function getObjectClass()
	{
		if (is_null($this->value) || !is_object($this->value))
		{
			return null;
		}
		return get_class($this->value);
	}

	public function getSerializationValueState()
	{
		if (!is_null($this->serializationData))
		{
			return self::VALUE_IS_SERIALIZED;
		}
		elseif (is_null($this->value))
		{
			return self::VALUE_IS_NULL;
		}
		elseif (is_object($this->value))
		{
			return self::VALUE_IS_OBJECT;
		}
		elseif (is_array($this->value))
		{
			return self::VALUE_IS_ARRAY;
		}
		else
		{
			return self::VALUE_IS_VALUE_TYPE;
		}
	}

	public function getDeserializationValueState()
	{
		if (!is_null($this->value))
		{
			return self::VALUE_IS_DESERIALIZED;
		}
		elseif (is_array($this->serializationData) && array_key_exists('@class', $this->serializationData))
		{
			return self::VALUE_IS_OBJECT;
		}
		elseif (is_array($this->serializationData))
		{
			return self::VALUE_IS_ARRAY;
		}
		elseif (!is_null($this->serializationData))
		{
			return self::VALUE_IS_VALUE_TYPE;
		}
		else
		{
			return self::VALUE_IS_NULL;
		}
	}
}