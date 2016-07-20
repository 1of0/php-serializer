<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use InvalidArgumentException;
use OneOfZero\Json\Helpers\Flags;
use OneOfZero\Json\Helpers\Metadata;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;

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
	
	public function testMetadataHelper()
	{
		$input = (object)[ 'foo' => 'bar' ];
		
		$this->assertEquals(null, Metadata::get($input, 'bar'));
		$this->assertEquals('bar', Metadata::get($input, 'foo'));
		
		Metadata::set($input, 'bar', 'baz');

		$this->assertEquals('baz', Metadata::get($input, 'bar'));
	}
	
	public function testMetadataHelperNullTarget()
	{
		$this->setExpectedException(InvalidArgumentException::class);
		
		$input = null;
		Metadata::set($input, 'foo', 'bar');
	}
	
	public function testMetadataHelperInvalidTarget()
	{
		$this->setExpectedException(InvalidArgumentException::class);
		
		$input = new SimpleClass('foo', 'bar', 'baz');
		Metadata::set($input, 'foo', 'bar');
	}
}
