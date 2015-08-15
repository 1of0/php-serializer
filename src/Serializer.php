<?php


namespace OneOfZero\Json;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use OneOfZero\Json\Internals\MemberWalker;

class Serializer
{
	const ANNOTATION_NAMESPACE = 'OneOfZero\\Json\\Annotations';
	const ANNOTATION_DIRECTORY = __DIR__ . '/Annotations';

	/**
	 * @var Serializer $instance
	 */
	private static $instance;

	/**
	 * @return Serializer
	 */
	public static function get()
	{
		if (!self::$instance)
		{
			self::$instance = new Serializer();
		}
		return self::$instance;
	}

	/**
	 * @var AnnotationReader $annotationReader
	 */
	private $annotationReader;

	/**
	 * @var Configuration $configuration
	 */
	private $configuration;

	/**
	 * @var MemberWalker $walker
	 */
	private $walker;

	/**
	 * @param Configuration|null $configuration
	 */
	public function __construct(Configuration $configuration = null)
	{
		$this->configuration = $configuration ? $configuration : new Configuration();

		// TODO: Use cached reader
		AnnotationRegistry::registerLoader(array(require __DIR__ . '/../vendor/autoload.php', 'loadClass'));
		$this->annotationReader = new AnnotationReader();

		$this->walker = new MemberWalker($this->configuration, $this->annotationReader);
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	public function serialize($data)
	{
		if (is_object($data))
		{
			return $this->jsonEncode($this->walker->serializeMembers($data));
		}

		if (is_array($data))
		{
			return $this->jsonEncode($this->walker->serializeArray($data));
		}

		return $this->jsonEncode($data);
	}

	public function deserialize($json)
	{
		$deserializedData = $this->jsonDecode($json);

		if (is_object($deserializedData))
		{
			return $this->walker->deserializeMembers($deserializedData);
		}

		if (is_array($deserializedData))
		{
			return $this->walker->deserializeArray($deserializedData);
		}

		return $deserializedData;
	}

	private function jsonEncode($data)
	{
		$options = 0;
		if ($this->configuration->prettyPrint)
		{
			$options |= JSON_PRETTY_PRINT;
		}
		return json_encode($data, $options, $this->configuration->maxDepth);
	}

	private function jsonDecode($json, $assoc = false)
	{
		return json_decode($json, $assoc, $this->configuration->maxDepth);
	}
}