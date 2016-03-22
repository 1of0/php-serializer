<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

use OneOfZero\Json\Enums\IncludeStrategy;
use OneOfZero\Json\Enums\OnMaxDepth;
use OneOfZero\Json\Enums\OnRecursion;
use OneOfZero\Json\Enums\ReferenceResolutionStrategy;

class Configuration
{
	/**
	 * When enabled, a MissingTypeException will be thrown if the provided type hint or embedded type cannot be found.
	 * Otherwise the type information will be disregarded.
	 *
	 * @var bool $strictTypeResolution
	 */
	public $strictTypeResolution = false;

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
}
