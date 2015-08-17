<?php


namespace OneOfZero\Json;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use OneOfZero\Json\Internals\MemberWalker;
use OneOfZero\Json\Internals\SerializationContext;

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
	 * @var SerializationContext $context
	 */
	private $context;

	/**
	 * @param Configuration|null $configuration
	 */
	public function __construct(Configuration $configuration = null)
	{
		$this->context = new SerializationContext();

		$this->context->serializer = $this;
		$this->context->configuration = $configuration ? $configuration : new Configuration();

		// TODO: Use cached reader
		AnnotationRegistry::registerLoader(array(require __DIR__ . '/../vendor/autoload.php', 'loadClass'));
		$this->context->annotationReader = new AnnotationReader();
		$this->context->memberWalker = new MemberWalker($this->context);
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	public function serialize($data)
	{
		return $this->jsonEncode($this->context->memberWalker->serialize($data));
	}

	/**
	 * @param string $json
	 * @param string|null $typeHint
	 * @return mixed
	 */
	public function deserialize($json, $typeHint = null)
	{
		$deserializedData = $this->jsonDecode($json);

		if (is_object($deserializedData) || is_array($deserializedData))
		{
			return $this->context->memberWalker->deserialize($deserializedData, $typeHint);
		}

		return $deserializedData;
	}

	private function jsonEncode($data)
	{
		$options = 0;
		if ($this->context->configuration->prettyPrint)
		{
			$options |= JSON_PRETTY_PRINT;
		}
		return json_encode($data, $options, $this->context->configuration->maxDepth);
	}

	private function jsonDecode($json)
	{
		return json_decode($json, false, $this->context->configuration->maxDepth);
	}
}