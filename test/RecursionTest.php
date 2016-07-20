<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Enums\OnMaxDepth;
use OneOfZero\Json\Enums\OnRecursion;
use OneOfZero\Json\Exceptions\NotSupportedException;
use OneOfZero\Json\Exceptions\RecursionException;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\RecursiveReferableClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use stdClass;

class RecursionTest extends AbstractTest
{
	public function testRecursionExpectException()
	{
		$this->setExpectedExceptionRegExp(RecursionException::class, '/.*Infinite.*/');
		
		$config = new Configuration();
		$config->defaultRecursionHandlingStrategy = OnRecursion::THROW_EXCEPTION;

		$input = new SimpleClass(null, null, null);
		$input->foo = $input;
		
		$serializer = new Serializer($config);
		$serializer->serialize($input);
	}
	
	public function testRecursionExpectReference()
	{
		$config = new Configuration();
		$config->defaultRecursionHandlingStrategy = OnRecursion::CREATE_REFERENCE;
		
		$input = new RecursiveReferableClass(123);
		$input->foo = new SimpleClass($input, 'bar', 'baz');
		
		$expectedOutput = json_encode([
			'@type'    => RecursiveReferableClass::class,
			'foo'       => [
				'@type'    => SimpleClass::class,
				'foo'       => [
					'@type'    => RecursiveReferableClass::class,
					'id'        => 123,
				],
				'bar'       => 'bar',
				'baz'       => 'baz',
			],
			'id'        => 123,
		]);
		
		$serializer = new Serializer($config);
		$json = $serializer->serialize($input);
		
		$this->assertEquals($expectedOutput, $json);
	}
	
	public function testRecursionExpectNull()
	{
		$config = new Configuration();
		$config->defaultRecursionHandlingStrategy = OnRecursion::SET_NULL;

		$input = new SimpleClass(null, null, null);
		$input->foo = $input;
		
		$serializer = new Serializer($config);
		$json = $serializer->serialize($input);
		$this->assertEquals('null', $json);
	}
	
	public function testRecursionExpectMaxDepthException()
	{
		$this->setExpectedExceptionRegExp(RecursionException::class, '/.*depth.*/');

		$config = new Configuration();
		$config->maxDepth = 5;
		$config->defaultRecursionHandlingStrategy = OnRecursion::CONTINUE_MAPPING;

		$input = new SimpleClass(null, null, null);
		$input->foo = $input;

		$serializer = new Serializer($config);
		$serializer->serialize($input);
	}
	
	public function testRecursionExpectMaxDepthSetNull()
	{
		$config = new Configuration();
		$config->maxDepth = 5;
		$config->defaultRecursionHandlingStrategy = OnRecursion::CONTINUE_MAPPING;
		$config->defaultMaxDepthHandlingStrategy = OnMaxDepth::SET_NULL;

		$input = new SimpleClass(null, 'bar', 'baz');
		$input->foo = $input;

		$serializer = new Serializer($config);
		$json = $serializer->serialize($input);
		
		$deserialized = json_decode($json);
		
		$counter = 1;
		$child = $deserialized;
		
		while (property_exists($child, 'foo'))
		{
			$counter++;
			$child = $child->foo;
		}
		
		$this->assertEquals($config->maxDepth, $counter);
	}
	
	public function testInvalidRecursionStrategy()
	{
		$this->setExpectedException(NotSupportedException::class);
		
		$config = new Configuration();
		$config->defaultRecursionHandlingStrategy = 1337;

		$input = new SimpleClass(null, 'bar', 'baz');
		$input->foo = $input;

		$serializer = new Serializer($config);
		$serializer->serialize($input);
	}
	
	public function testInvalidMaxDepthStrategy()
	{
		$this->setExpectedException(NotSupportedException::class);
		
		$config = new Configuration();
		$config->defaultRecursionHandlingStrategy = OnRecursion::CONTINUE_MAPPING;
		$config->defaultMaxDepthHandlingStrategy = 1337;

		$input = new SimpleClass(null, 'bar', 'baz');
		$input->foo = $input;

		$serializer = new Serializer($config);
		$serializer->serialize($input);
	}
	
	public function testRecursiveStdClass()
	{
		$this->setExpectedExceptionRegExp(RecursionException::class, '/.*Infinite.*/');

		$config = new Configuration();
		$config->defaultRecursionHandlingStrategy = OnRecursion::THROW_EXCEPTION;

		$input = new stdClass();
		$input->foo = $input;

		$serializer = new Serializer($config);
		$serializer->serialize($input);
	}
}
