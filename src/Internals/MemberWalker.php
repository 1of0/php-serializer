<?php


namespace OneOfZero\Json\Internals;


use Doctrine\Common\Annotations\AnnotationReader;
use OneOfZero\Json\Annotations\NoMetaData;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Internals\AnnotationHandlers\AbstractHandler;
use OneOfZero\Json\Internals\AnnotationHandlers\JsonConverterHandler;
use OneOfZero\Json\Internals\AnnotationHandlers\JsonGetterHandler;
use OneOfZero\Json\Internals\AnnotationHandlers\JsonIgnoreHandler;
use OneOfZero\Json\Internals\AnnotationHandlers\JsonPropertyHandler;
use OneOfZero\Json\Internals\AnnotationHandlers\JsonSetterHandler;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class MemberWalker
{
	const CLASS_TAG = '@class';

	private static $registeredAnnotationHandlers =
	[
		JsonIgnoreHandler::class,
		JsonPropertyHandler::class,
		JsonGetterHandler::class,
		JsonSetterHandler::class,
		JsonConverterHandler::class
	];

	/**
	 * @var Configuration $configuration
	 */
	private $configuration;

	/**
	 * @var AnnotationReader $annotationReader
	 */
	private $annotationReader;

	/**
	 * @var AbstractHandler[] $targetedAnnotationHandlers
	 */
	private $targetedAnnotationHandlers = [];

	/**
	 * @var AbstractHandler[] $genericAnnotationHandlers
	 */
	private $genericAnnotationHandlers = [];

	/**
	 * @var string[] $annotationHandlerClasses
	 */
	private $annotationHandlerClasses = [];

	/**
	 * @param Configuration $configuration
	 * @param AnnotationReader $annotationReader
	 */
	public function __construct(Configuration $configuration, AnnotationReader $annotationReader)
	{
		$this->configuration = $configuration;
		$this->annotationReader = $annotationReader;
		$this->loadAnnotationHandlers();
	}

	/**
	 * @param object $object
	 * @return array
	 */
	public function serializeMembers($object)
	{
		$class = new ReflectionClass($object);
		$members = $this->getMembers($class, $object);

		foreach ($members as $key => $member)
		{
			foreach($member->getAnnotations() as $annotation)
			{
				$annotationHandler = $this->getAnnotationHandler(get_class($annotation));

				if ($annotationHandler && !$annotationHandler->handleSerialization($class, $annotation, $member))
				{
					unset($members[$key]);
					break;
				}
			}

			foreach ($this->genericAnnotationHandlers as $annotationHandler)
			{
				if ($annotationHandler && !$annotationHandler->handleSerialization($class, null, $member))
				{
					unset($members[$key]);
					break;
				}
			}

			if (!$this->configuration->includeNullValues && is_null($member->serializationData) && is_null($member->value))
			{
				unset($members[$key]);
			}
		}

		$serializationData = [];

		if (!$this->annotationReader->getClassAnnotation($class, NoMetaData::class))
		{
			// TODO: Use type index and type hash?
			$serializationData[self::CLASS_TAG] = $class->name;
		}


		foreach ($members as $member)
		{
			switch($member->getSerializationValueState())
			{
				case Member::VALUE_IS_NULL:         $value = null; break;
				case Member::VALUE_IS_ARRAY:        $value = $this->serializeArray($member->value); break;
				case Member::VALUE_IS_OBJECT:       $value = $this->serializeMembers($member->value); break;
				case Member::VALUE_IS_SERIALIZED:   $value = $member->serializationData; break;
				default:                            $value = $member->value;
			}

			$serializationData[$member->getPropertyName()] = $value;
		}
		return $serializationData;
	}

	/**
	 * @param array $array
	 * @return array
	 */
	public function serializeArray(array $array)
	{
		$result = [];
		foreach ($array as $key => $item)
		{
			if (is_null($item))
			{
				$result[$key] = null;
				continue;
			}

			if (is_array($item))
			{
				/** @var array $item */
				$result[$key] = $this->serializeArray($item);
			}

			if (is_object($item))
			{
				/** @var object $item */
				$result[$key] = $this->serializeMembers($item);
				continue;
			}

			$result[$key] = $item;
		}
		return $result;
	}

	public function deserializeMembers($deserializedData)
	{
		if (!array_key_exists(self::CLASS_TAG, $deserializedData))
		{
			return $this->deserializeArray((array)$deserializedData);
		}

		$class = new ReflectionClass($deserializedData->{self::CLASS_TAG});
		$members = $this->getMembers($class);

		foreach ($members as $key => $member)
		{
			foreach($member->getAnnotations() as $annotation)
			{
				$annotationHandler = $this->getAnnotationHandler(get_class($annotation));

				if ($annotationHandler && !$annotationHandler->handleDeserialization($class, $deserializedData, $annotation, $member))
				{
					unset($members[$key]);
					break;
				}
			}

			foreach ($this->genericAnnotationHandlers as $annotationHandler)
			{
				if ($annotationHandler && !$annotationHandler->handleDeserialization($class, $deserializedData, null, $member))
				{
					unset($members[$key]);
					break;
				}
			}

			if (array_key_exists($member->getPropertyName(), $deserializedData))
			{
				$member->serializationData = $deserializedData->{$member->getPropertyName()};
			}
		}

		foreach ($members as $member)
		{
			switch($member->getDeserializationValueState())
			{
				case Member::VALUE_IS_DESERIALIZED: $value = $member->value; break;
				case Member::VALUE_IS_ARRAY:        $value = $this->deserializeArray($member->value); break;
				case Member::VALUE_IS_OBJECT:       $value = $this->deserializeMembers($member->serializationData); break;
				default:                            $value = $member->serializationData;
			}

			$member->value = $value;
		}

		$instance = $class->newInstance();

		foreach ($members as $member)
		{
			if ($member->type == Member::TYPE_PROPERTY)
			{
				$class->getProperty($member->name)->setValue($instance, $member->value);
			}

			if ($member->type == Member::TYPE_METHOD)
			{
				$class->getMethod($member->name)->invoke($instance, $member->value);
			}
		}

		return $instance;
	}

	public function deserializeArray($deserializedData)
	{
		$result = [];
		foreach ($deserializedData as $key => $item)
		{
			if (is_null($item))
			{
				$result[$key] = null;
				continue;
			}

			if (is_array($item))
			{
				/** @var array $item */
				$result[$key] = $this->deserializeArray($item);
			}

			if (is_object($item))
			{
				/** @var object $item */
				$result[$key] = $this->deserializeMembers($item);
				continue;
			}

			$result[$key] = $item;
		}
		return $result;
	}

	private function getAnnotationHandler($annotationClass)
	{
		if (array_key_exists($annotationClass, $this->targetedAnnotationHandlers))
		{
			return $this->targetedAnnotationHandlers[$annotationClass];
		}
		return null;
	}

	private function loadAnnotationHandlers()
	{
		foreach (self::$registeredAnnotationHandlers as $handlerClass)
		{
			$this->loadAnnotationHandler($handlerClass);
		}
	}

	private function loadAnnotationHandler($handlerClass, $visited = [])
	{
		if (in_array($handlerClass, $this->annotationHandlerClasses))
		{
			return;
		}

		$visited[] = $handlerClass;

		/** @var AbstractHandler $instance */
		$instance = new $handlerClass($this->configuration, $this->annotationReader);

		foreach ($instance->dependsOn() as $dependency)
		{
			if (in_array($dependency, $visited))
			{
				$visitationMap = sprintf('[ %s, %s ]', implode(', ', $visited), $dependency);
				throw new SerializationException(
					"Cyclic dependency detected in annotation handlers. Visitation map: $visitationMap"
				);
			}

			$this->loadAnnotationHandler($dependency, $visited);
		}

		$targetAnnotation = $instance->targetAnnotation();

		if ($targetAnnotation)
		{
			$this->targetedAnnotationHandlers[$targetAnnotation] = $instance;
		}
		else
		{
			$this->genericAnnotationHandlers[] = $instance;
		}

		$this->annotationHandlerClasses[] = $handlerClass;
	}

	/**
	 * @param ReflectionClass $class
	 * @param $object
	 * @return Member[]
	 */
	private function getMembers(ReflectionClass $class, $object = null)
	{
		$members = [];

		foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
		{
			$member = new Member(
				$object,
				$property->name,
				Member::TYPE_PROPERTY,
				$this->annotationReader->getPropertyAnnotations($property)
			);

			// If the object is provided, set is as member value
			if (!is_null($object))
			{
				$member->value = $property->getValue($object);
			}

			$members[] = $member;
		}

		foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			// Skip magic methods
			if (strpos($method->name, '__') === 0)
			{
				continue;
			}

			$members[] = new Member(
				$object,
				$method->name,
				Member::TYPE_METHOD,
				$this->annotationReader->getMethodAnnotations($method)
			);
		}
		return $members;
	}
}