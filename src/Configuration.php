<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json;

class Configuration
{
	const RESOLVE_LAZY = 0;
	const RESOLVE_EAGER = 1;

	const INCLUDE_PUBLIC_PROPERTIES     = 0b00000001;
	const INCLUDE_PUBLIC_GETTERS        = 0b00000010;
	const INCLUDE_PUBLIC_SETTERS        = 0b00000100;
	const INCLUDE_NON_PUBLIC_PROPERTIES = 0b00001000;
	const INCLUDE_NON_PUBLIC_GETTERS    = 0b00010000;
	const INCLUDE_NON_PUBLIC_SETTERS    = 0b00100000;
	
	const INCLUDE_ALL_PUBLIC            = 0b00000111;
	const INCLUDE_ALL_NON_PUBLIC        = 0b00111000;

	/**
	 * Specifies one or more kinds of members that will be automatically included during serialization.
	 *
	 * The value uses bit flags, so you may use the bitwise OR (|) to specify multiple member kinds.
	 *
	 * @var int $defaultMemberInclusionStrategy
	 */
	public $defaultMemberInclusionStrategy = self::INCLUDE_PUBLIC_PROPERTIES;

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
	public $defaultReferenceResolutionStrategy = self::RESOLVE_LAZY;
}
