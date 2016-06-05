<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Helpers\Metadata;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use OneOfZero\Json\Visitors\DeserializingVisitor;

class DeserializingVisitorTest extends AbstractTest
{
	public function testScalarValueArray()
	{
		$input = [ 'a', 'b', 'c' ];

		$output = $this->createVisitor()->visit($input);
		$this->assertSequenceEquals($input, $output);
	}
	
	public function testObjectArray()
	{
		$input = [
			(object)[ Metadata::TYPE => SimpleClass::class, 'foo' => 'foo' ],
			(object)[ Metadata::TYPE => SimpleClass::class, 'foo' => 'bar' ],
			(object)[ Metadata::TYPE => SimpleClass::class, 'foo' => 'baz' ],
		];

		$output = $this->createVisitor()->visit($input);

		$this->assertInstanceOf(SimpleClass::class, $output[0]);
		$this->assertInstanceOf(SimpleClass::class, $output[1]);
		$this->assertInstanceOf(SimpleClass::class, $output[2]);

		$this->assertEquals('foo', $output[0]->foo);
		$this->assertEquals('bar', $output[1]->foo);
		$this->assertEquals('baz', $output[2]->foo);
	}

	public function testMixedArray()
	{
		$input = [
			'abc',
			123,
			(object)[ Metadata::TYPE => SimpleClass::class, 'foo' => 'baz' ],
		];

		$output = $this->createVisitor()->visit($input);

		$this->assertInstanceOf(SimpleClass::class, $output[2]);

		$this->assertEquals('abc', $output[0]);
		$this->assertEquals(123, $output[1]);
		$this->assertEquals('baz', $output[2]->foo);
	}

	public function testSimpleObject()
	{
		$input = (object)[
			Metadata::TYPE  => SimpleClass::class,
			'foo'           => 'abc',
			'bar'           => '123',
		];

		$output = $this->createVisitor()->visit($input);

		$this->assertInstanceOf(SimpleClass::class, $output);
		$this->assertEquals('abc', $output->foo);
		$this->assertEquals('123', $output->bar);
	}

	public function testObjectWithArray()
	{
		$input = (object)[
			Metadata::TYPE  => SimpleClass::class,
			'foo'           => [ 'foo', 'bar', 'baz' ],
			'bar'           => (object)
			[
				Metadata::TYPE  => SimpleClass::class,
				'foo'           => '123'
			]
		];

		$output = $this->createVisitor()->visit($input);

		$this->assertInstanceOf(SimpleClass::class, $output);
		$this->assertInstanceOf(SimpleClass::class, $output->bar);

		$this->assertSequenceEquals([ 'foo', 'bar', 'baz' ], $output->foo);
		$this->assertEquals(123, $output->bar->foo);
	}

	private function createVisitor()
	{
		return new DeserializingVisitor(clone $this->defaultConfiguration, clone $this->defaultFactoryChain);
	}
}
