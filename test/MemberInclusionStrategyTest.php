<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Enums\IncludeStrategy;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\DifferentVisibilityClass;

class MemberInclusionStrategyTest extends AbstractTest
{
	public function testOnlyPublicPropertiesStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::PUBLIC_PROPERTIES);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@class' => DifferentVisibilityClass::class,
			'publicProperty' => 'foo',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);
		
		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertEquals('foo', $deserialized->getPublicProperty());
		$this->assertNull($deserialized->getProtectedProperty());
		$this->assertNull($deserialized->getPrivateProperty());
		$this->assertNull($deserialized->_getPublicMethod());
		$this->assertNull($deserialized->_getProtectedMethod());
		$this->assertNull($deserialized->_getPrivateMethod());
	}

	public function testOnlyNonPublicPropertiesStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::NON_PUBLIC_PROPERTIES);
		$input = $this->createInput();
		
		$expectedOutput = json_encode([
			'@class' => DifferentVisibilityClass::class,
			'protectedProperty' => 'bar',
			'privateProperty' => 'baz',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->getPublicProperty());
		$this->assertEquals('bar', $deserialized->getProtectedProperty());
		$this->assertEquals('baz', $deserialized->getPrivateProperty());
		$this->assertNull($deserialized->_getPublicMethod());
		$this->assertNull($deserialized->_getProtectedMethod());
		$this->assertNull($deserialized->_getPrivateMethod());
	}

	public function testOnlyPropertiesStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL_PROPERTIES);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@class' => DifferentVisibilityClass::class,
			'publicProperty' => 'foo',
			'protectedProperty' => 'bar',
			'privateProperty' => 'baz',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);
		$this->assertEquals('foo', $deserialized->getPublicProperty());
		$this->assertEquals('bar', $deserialized->getProtectedProperty());
		$this->assertEquals('baz', $deserialized->getPrivateProperty());
		$this->assertNull($deserialized->_getPublicMethod());
		$this->assertNull($deserialized->_getProtectedMethod());
		$this->assertNull($deserialized->_getPrivateMethod());
	}

	public function testOnlyPublicGettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::PUBLIC_GETTERS);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@class' => DifferentVisibilityClass::class,
			'publicMethod' => '1234',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->getPublicProperty());
		$this->assertNull($deserialized->getProtectedProperty());
		$this->assertNull($deserialized->getPrivateProperty());
		$this->assertNull($deserialized->_getPublicMethod());
		$this->assertNull($deserialized->_getProtectedMethod());
		$this->assertNull($deserialized->_getPrivateMethod());
	}

	public function testOnlyNonPublicGettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::NON_PUBLIC_GETTERS);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@class' => DifferentVisibilityClass::class,
			'protectedMethod' => '5678',
			'privateMethod' => '9876',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->getPublicProperty());
		$this->assertNull($deserialized->getProtectedProperty());
		$this->assertNull($deserialized->getPrivateProperty());
		$this->assertNull($deserialized->_getPublicMethod());
		$this->assertNull($deserialized->_getProtectedMethod());
		$this->assertNull($deserialized->_getPrivateMethod());
	}

	public function testOnlyPublicSettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::PUBLIC_SETTERS);
		$input = $this->createInput();

		$serialized = $serializer->serialize($input);
		$this->assertEquals('null', $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize(json_encode([
			'@class' => DifferentVisibilityClass::class,
			'publicMethod' => '1234',
		]));

		$this->assertNull($deserialized->getPublicProperty());
		$this->assertNull($deserialized->getProtectedProperty());
		$this->assertNull($deserialized->getPrivateProperty());
		$this->assertEquals('1234', $deserialized->_getPublicMethod());
		$this->assertNull($deserialized->_getProtectedMethod());
		$this->assertNull($deserialized->_getPrivateMethod());
	}

	public function testOnlyNonPublicSettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::NON_PUBLIC_SETTERS);
		$input = $this->createInput();

		$serialized = $serializer->serialize($input);
		$this->assertEquals('null', $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize(json_encode([
			'@class' => DifferentVisibilityClass::class,
			'protectedMethod' => '5678',
			'privateMethod' => '9876',
		]));

		$this->assertNull($deserialized->getPublicProperty());
		$this->assertNull($deserialized->getProtectedProperty());
		$this->assertNull($deserialized->getPrivateProperty());
		$this->assertNull($deserialized->_getPublicMethod());
		$this->assertEquals('5678', $deserialized->_getProtectedMethod());
		$this->assertEquals('9876', $deserialized->_getPrivateMethod());
	}

	public function testOnlyGettersAndSettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL_GETTERS_SETTERS);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@class' => DifferentVisibilityClass::class,
			'publicMethod' => '1234',
			'protectedMethod' => '5678',
			'privateMethod' => '9876',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->getPublicProperty());
		$this->assertNull($deserialized->getProtectedProperty());
		$this->assertNull($deserialized->getPrivateProperty());
		$this->assertEquals('1234', $deserialized->_getPublicMethod());
		$this->assertEquals('5678', $deserialized->_getProtectedMethod());
		$this->assertEquals('9876', $deserialized->_getPrivateMethod());
	}

	public function testOnlyPublicMembersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL_PUBLIC);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@class' => DifferentVisibilityClass::class,
			'publicProperty' => 'foo',
			'publicMethod' => '1234',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertEquals('foo', $deserialized->getPublicProperty());
		$this->assertNull($deserialized->getProtectedProperty());
		$this->assertNull($deserialized->getPrivateProperty());
		$this->assertEquals('1234', $deserialized->_getPublicMethod());
		$this->assertNull($deserialized->_getProtectedMethod());
		$this->assertNull($deserialized->_getPrivateMethod());
	}

	public function testOnlyNonPublicMembersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL_NON_PUBLIC);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@class' => DifferentVisibilityClass::class,
			'protectedProperty' => 'bar',
			'privateProperty' => 'baz',
			'protectedMethod' => '5678',
			'privateMethod' => '9876',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->getPublicProperty());
		$this->assertEquals('bar', $deserialized->getProtectedProperty());
		$this->assertEquals('baz', $deserialized->getPrivateProperty());
		$this->assertNull($deserialized->_getPublicMethod());
		$this->assertEquals('5678', $deserialized->_getProtectedMethod());
		$this->assertEquals('9876', $deserialized->_getPrivateMethod());
	}

	public function testAllMembersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@class' => DifferentVisibilityClass::class,
			'publicProperty' => 'foo',
			'protectedProperty' => 'bar',
			'privateProperty' => 'baz',
			'publicMethod' => '1234',
			'protectedMethod' => '5678',
			'privateMethod' => '9876',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertEquals('foo', $deserialized->getPublicProperty());
		$this->assertEquals('bar', $deserialized->getProtectedProperty());
		$this->assertEquals('baz', $deserialized->getPrivateProperty());
		$this->assertEquals('1234', $deserialized->_getPublicMethod());
		$this->assertEquals('5678', $deserialized->_getProtectedMethod());
		$this->assertEquals('9876', $deserialized->_getPrivateMethod());
	}

	public function testNoneStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::NONE);
		$input = $this->createInput();

		$serialized = $serializer->serialize($input);
		$this->assertEquals('null', $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize("{}", DifferentVisibilityClass::class);

		$this->assertNull($deserialized->getPublicProperty());
		$this->assertNull($deserialized->getProtectedProperty());
		$this->assertNull($deserialized->getPrivateProperty());
		$this->assertNull($deserialized->_getPublicMethod());
		$this->assertNull($deserialized->_getProtectedMethod());
		$this->assertNull($deserialized->_getPrivateMethod());
	}

	/**
	 * @return DifferentVisibilityClass
	 */
	private function createInput()
	{
		return new DifferentVisibilityClass(
			'foo', 'bar', 'baz',
			'1234', '5678', '9876'
		);
	}

	/**
	 * @param int $strategy
	 *
	 * @return Serializer
	 */
	private function createSerializer($strategy)
	{
		$configuration = new Configuration();
		$configuration->defaultMemberInclusionStrategy = $strategy;

		return new Serializer($configuration);
	}
}