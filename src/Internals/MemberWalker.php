<?php


namespace OneOfZero\Json\Internals;


use Doctrine\Common\Annotations\AnnotationReader;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Internals\AnnotationHandlers\AbstractHandler;
use OneOfZero\Json\Internals\AnnotationHandlers\JsonIgnoreHandler;
use OneOfZero\Json\Internals\AnnotationHandlers\JsonPropertyHandler;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class MemberWalker
{
	private static $annotationHandlerClasses =
	[
		JsonIgnoreHandler::class,
		JsonPropertyHandler::class
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
	 * @var AbstractHandler[] $annotationHandlers
	 */
	private $annotationHandlers;

	/**
	 * @param Configuration $configuration
	 * @param AnnotationReader $annotationReader
	 */
	public function __construct(Configuration $configuration, AnnotationReader $annotationReader)
	{
		$this->configuration = $configuration;
		$this->annotationReader = $annotationReader;
		$this->annotationHandlers = $this->loadAnnotationHandlers();
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

			if (!$this->configuration->includeNullValues && is_null($member->serializationData) && is_null($member->value))
			{
				unset($members[$key]);
			}
		}

		$serializationData = [ '@class' => $class->name ];
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

	public function deserializeMembers(array $serializedData)
	{
		$class = new ReflectionClass($serializedData['@class']);
		$members = $this->getMembers($class);

		foreach ($members as $key => $member)
		{
			foreach($member->getAnnotations() as $annotation)
			{
				$annotationHandler = $this->getAnnotationHandler(get_class($annotation));

				if ($annotationHandler && !$annotationHandler->handleDeserialization($class, $serializedData, $annotation, $member))
				{
					unset($members[$key]);
					break;
				}
			}

			if (array_key_exists($member->getPropertyName(), $serializedData))
			{
				$member->serializationData = $serializedData[$member->getPropertyName()];
			}
		}

		foreach ($members as $member)
		{
			switch($member->getDeserializationValueState())
			{
				//case Member::VALUE_IS_ARRAY:        $value = $this->serializeArray($member->value); break;
				case Member::VALUE_IS_DESERIALIZED: $value = $member->value; break;
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

	private function getAnnotationHandler($annotationClass)
	{
		if (array_key_exists($annotationClass, $this->annotationHandlers))
		{
			return $this->annotationHandlers[$annotationClass];
		}
		return null;
	}

	private function loadAnnotationHandlers()
	{
		$handlers = [];
		foreach (self::$annotationHandlerClasses as $handlerClass)
		{
			/** @var AbstractHandler $instance */
			$instance = new $handlerClass($this->configuration, $this->annotationReader);

			$handlers[$instance->handlesAnnotation()] = $instance;
		}
		return $handlers;
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

			if (!is_null($object))
			{
				$member->value = $property->getValue($object);
			}

			$members[] = $member;
		}

		foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
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