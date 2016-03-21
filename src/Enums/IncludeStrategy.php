<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Enums;

class IncludeStrategy
{
	const NONE                  = 0b00000000;
	const ALL                   = 0b11111111;

	const PUBLIC_PROPERTIES     = 0b00000001;
	const PUBLIC_GETTERS        = 0b00000010;
	const PUBLIC_SETTERS        = 0b00000100;
	const NON_PUBLIC_PROPERTIES = 0b00001000;
	const NON_PUBLIC_GETTERS    = 0b00010000;
	const NON_PUBLIC_SETTERS    = 0b00100000;

	const ALL_PUBLIC            = 0b00000111;
	const ALL_NON_PUBLIC        = 0b00111000;

	const ALL_PROPERTIES        = 0b00001001;
	const ALL_GETTERS           = 0b00010010;
	const ALL_SETTERS           = 0b00100100;
	const ALL_GETTERS_SETTERS   = 0b00110110;
}