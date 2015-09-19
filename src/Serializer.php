<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

use OneOfZero\Json\DependencyInjection\ContainerAdapterInterface;
use OneOfZero\Json\Internals\MemberWalker;
use OneOfZero\Json\Internals\ProxyHelper;
use OneOfZero\Json\Internals\SerializerContext;

class Serializer
{
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
	 * @var SerializerContext $context
	 */
	private $context;

	/**
	 * @param ContainerAdapterInterface $containerAdapter
	 * @param Configuration|null $configuration
	 */
	public function __construct(ContainerAdapterInterface $containerAdapter = null, Configuration $configuration = null)
	{
		$this->context = new SerializerContext();
		$this->context->setSerializer($this);
		$this->context->setConfiguration($configuration ? $configuration : new Configuration());
		$this->context->setContainer($containerAdapter);
		$this->context->setMemberWalker(new MemberWalker($this->context));
		$this->context->setProxyHelper(new ProxyHelper($this->context->getReferenceResolver()));
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	public function serialize($data)
	{
		return $this->jsonEncode($this->context->getMemberWalker()->serialize($data));
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
			return $this->context->getMemberWalker()->deserialize($deserializedData, $typeHint);
		}

		return $deserializedData;
	}

	/**
	 * @param object $instance
	 * @param string $type
	 * @return object
	 */
	public function cast($instance, $type)
	{
		return $this->deserialize($this->serialize($instance), $type);
	}

	/**
	 * @return Configuration
	 */
	public function getConfiguration()
	{
		return $this->context->getConfiguration();
	}

	/**
	 * @param Configuration $configuration
	 */
	public function setConfiguration(Configuration $configuration)
	{
		$this->context->setConfiguration($configuration);
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	private function jsonEncode($data)
	{
		$options = 0;
		if ($this->context->getConfiguration()->prettyPrint)
		{
			$options |= JSON_PRETTY_PRINT;
		}
		return json_encode($data, $options, $this->context->getConfiguration()->maxDepth);
	}

	/**
	 * @param string $json
	 * @return mixed
	 */
	private function jsonDecode($json)
	{
		return json_decode($json, false, $this->context->getConfiguration()->maxDepth);
	}
}
