<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Helpers\Flags;

class HelpersTest extends AbstractTest
{
	public function testFlagHelper()
	{
		$this->assertTrue(Flags::has(0b11111111, 0b00001111));
		$this->assertEquals(0b11111111, Flags::add(0b11110000, 0b00001111));
		$this->assertEquals(0b11110000, Flags::remove(0b11111111, 0b00001111));
		$this->assertEquals(0b11110001, Flags::toggle(0b11110000, 0b00000001));

		$this->assertEquals(0b00001111, Flags::invert(0b11110000));
		$this->assertEquals(0b0011, Flags::invert(0b1100, 4));
		$this->assertEquals(0b1, Flags::invert(0b0, 1));
		$this->assertEquals(0, Flags::invert(0b0, 0));
	}
}