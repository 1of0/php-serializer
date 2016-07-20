<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

use Interop\Container\ContainerInterface;
use OneOfZero\Json\ContractResolvers\ContractResolverInterface;
use OneOfZero\Json\Enums\IncludeStrategy;
use OneOfZero\Json\Enums\OnMaxDepth;
use OneOfZero\Json\Enums\OnRecursion;
use OneOfZero\Json\Enums\ReferenceResolutionStrategy;

/**
 * The Configuration class provides various options that allow you to control the behaviour of the serializer. 
 */
class Configuration
{
	/**
	 * @var MetaHintWhitelist $metaHintWhitelist
	 */
	private $metaHintWhitelist;

	/**
	 * @var ConverterConfiguration $converters
	 */
	private $converters;
	
	/**
	 * When enabled, a MissingTypeException will be thrown if the provided type hint or embedded type cannot be found.
	 * Otherwise the type information will be disregarded.
	 *
	 * @var bool $strictTypeResolution
	 */
	public $strictTypeResolution = false;

	/**
	 * When enabled, type information will be embedded in serialized objects. This type information can be  
	 *
	 * @var bool $embedTypeMetadata
	 */
	public $embedTypeMetadata = true;

	/**
	 * Enable/disable pretty JSON printing.
	 *
	 * @var bool $prettyPrint
	 */
	public $prettyPrint = false;

	/**
	 * Option flags that are passed to the internally used json_encode() and json_decode() functions.
	 *
	 * @var int $jsonEncodeOptions
	 */
	public $jsonEncodeOptions = 0;

	/**
	 * Specifies whether members with null values should be included in serialization.
	 *
	 * @var bool $includeNullValues
	 */
	public $includeNullValues = false;

	/**
	 * Specifies the maximum serialization depth for the internally used json_encode() and json_decode() functions.
	 * 
	 * @var int $maxDepth
	 */
	public $maxDepth = 32;

	/**
	 * Specifies the default strategy for resolving references when deserializing.
	 * 
	 * @var int $defaultReferenceResolutionStrategy
	 */
	public $defaultReferenceResolutionStrategy = ReferenceResolutionStrategy::LAZY;

	/**
	 * Specifies one or more kinds of members that will be automatically included during serialization.
	 *
	 * The value uses bit flags, so you may use the bitwise OR (|) to specify multiple member kinds.
	 *
	 * @var int $defaultMemberInclusionStrategy
	 */
	public $defaultMemberInclusionStrategy = IncludeStrategy::PUBLIC_PROPERTIES;

	/**
	 * Specifies the default handling strategy that will be used when recursion is detected during serialization.
	 * 
	 * @var int $defaultRecursionHandlingStrategy
	 */
	public $defaultRecursionHandlingStrategy = OnRecursion::THROW_EXCEPTION;
	
	/**
	 * Specifies the default handling strategy that will be used when the maximum depth is reached.
	 * 
	 * @var int $defaultMaxDepthHandlingStrategy
	 */
	public $defaultMaxDepthHandlingStrategy = OnMaxDepth::THROW_EXCEPTION;

	/**
	 * Configures the contract resolver to use during serialization and deserialization.
	 * 
	 * @var ContractResolverInterface|null $contractResolver
	 */
	public $contractResolver = null;

	/**
	 * Configures the whitelist that should be used to determine which classes may be used as meta type hints during
	 * deserialization.
	 * 
	 * @return MetaHintWhitelist
	 */
	public function getMetaHintWhitelist()
	{
		return $this->metaHintWhitelist;
	}

	/**
	 * Allows configuration of global and type-assigned converters.
	 * 
	 * @return ConverterConfiguration
	 */
	public function getConverters()
	{
		return $this->converters;
	}

	/**
	 * @param MetaHintWhitelist $metaHintWhitelist
	 */
	public function setMetaHintWhitelist(MetaHintWhitelist $metaHintWhitelist)
	{
		$this->metaHintWhitelist = $metaHintWhitelist;
	}

	/**
	 * @param ConverterConfiguration $converters
	 */
	public function setConverters(ConverterConfiguration $converters)
	{
		$this->converters = $converters;
	}

	/**
	 * @param ContainerInterface|null $container
	 * @param bool $loadDefaultConverters
	 */
	public function __construct(ContainerInterface $container = null, $loadDefaultConverters = true)
	{
		$this->metaHintWhitelist = new MetaHintWhitelist();
		$this->converters = new ConverterConfiguration($container, $loadDefaultConverters);
	}
	
	public function __clone()
	{
		$this->metaHintWhitelist = clone $this->metaHintWhitelist;
		$this->converters = clone $this->converters;
	}

	/**
	 * Returns a hash for this configuration.
	 * 
	 * @return string
	 */
	public function getHash()
	{
		return sha1(json_encode($this));
	}
}
