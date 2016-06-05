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
use OneOfZero\Json\Visitors\SerializingVisitor;

class SerializingVisitorTest extends AbstractTest
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
			new SimpleClass('foo', null, null), 
			new SimpleClass('bar', null, null), 
			new SimpleClass('baz', null, null) 
		];

		$expectedOutput = [
			[ Metadata::TYPE => SimpleClass::class, 'foo' => 'foo' ],
			[ Metadata::TYPE => SimpleClass::class, 'foo' => 'bar' ],
			[ Metadata::TYPE => SimpleClass::class, 'foo' => 'baz' ],
		];

		$output = $this->createVisitor()->visit($input);
		$this->assertSequenceEquals($expectedOutput, $output);
	}

	public function testMixedArray()
	{
		$input = [
			'abc', 
			123, 
			new SimpleClass('baz', null, null) 
		];

		$expectedOutput = [
			'abc',
			123,
			[ Metadata::TYPE => SimpleClass::class, 'foo' => 'baz' ],
		];

		$output = $this->createVisitor()->visit($input);
		$this->assertSequenceEquals($expectedOutput, $output);
	}

	public function testSimpleObject()
	{
		$input = new SimpleClass('abc', '123', '456');

		$expectedOutput = [
			Metadata::TYPE  => SimpleClass::class,
			'foo'           => 'abc',
			'bar'           => '123',
			'baz'           => '456',
		];

		$output = $this->createVisitor()->visit($input);
		$this->assertSequenceEquals($expectedOutput, $output);
	}

	public function testObjectWithArray()
	{
		$input = new SimpleClass([ 'foo', 'bar', 'baz' ], new SimpleClass('123', null, null), null);

		$expectedOutput = [
			Metadata::TYPE  => SimpleClass::class,
			'foo'           => [ 'foo', 'bar', 'baz' ],
			'bar'           => [
				Metadata::TYPE  => SimpleClass::class,
				'foo'           => '123'
			]
		];

		$output = $this->createVisitor()->visit($input);
		$this->assertSequenceEquals($expectedOutput, $output);
	}

	private function createVisitor()
	{
		return new SerializingVisitor(clone $this->defaultConfiguration, clone $this->defaultFactoryChain);
	}
}
