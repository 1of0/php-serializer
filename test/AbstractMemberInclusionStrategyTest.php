<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Enums\IncludeStrategy;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\DifferentVisibilityClass;

abstract class AbstractMemberInclusionStrategyTest extends AbstractTest
{
	public function testOnlyPublicPropertiesStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::PUBLIC_PROPERTIES);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@type' => DifferentVisibilityClass::class,
			'publicProperty' => 'foo',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);
		
		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertEquals('foo', $deserialized->__getPublicProperty());
		$this->assertNull($deserialized->__getProtectedProperty());
		$this->assertNull($deserialized->__getPrivateProperty());
		$this->assertNull($deserialized->__getPublicMethod());
		$this->assertNull($deserialized->__getProtectedMethod());
		$this->assertNull($deserialized->__getPrivateMethod());
	}

	public function testOnlyNonPublicPropertiesStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::NON_PUBLIC_PROPERTIES);
		$input = $this->createInput();
		
		$expectedOutput = json_encode([
			'@type' => DifferentVisibilityClass::class,
			'protectedProperty' => 'bar',
			'privateProperty' => 'baz',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->__getPublicProperty());
		$this->assertEquals('bar', $deserialized->__getProtectedProperty());
		$this->assertEquals('baz', $deserialized->__getPrivateProperty());
		$this->assertNull($deserialized->__getPublicMethod());
		$this->assertNull($deserialized->__getProtectedMethod());
		$this->assertNull($deserialized->__getPrivateMethod());
	}

	public function testOnlyPropertiesStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL_PROPERTIES);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@type' => DifferentVisibilityClass::class,
			'publicProperty' => 'foo',
			'protectedProperty' => 'bar',
			'privateProperty' => 'baz',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);
		$this->assertEquals('foo', $deserialized->__getPublicProperty());
		$this->assertEquals('bar', $deserialized->__getProtectedProperty());
		$this->assertEquals('baz', $deserialized->__getPrivateProperty());
		$this->assertNull($deserialized->__getPublicMethod());
		$this->assertNull($deserialized->__getProtectedMethod());
		$this->assertNull($deserialized->__getPrivateMethod());
	}

	public function testOnlyPublicGettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::PUBLIC_GETTERS);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@type' => DifferentVisibilityClass::class,
			'publicMethod' => '1234',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->__getPublicProperty());
		$this->assertNull($deserialized->__getProtectedProperty());
		$this->assertNull($deserialized->__getPrivateProperty());
		$this->assertNull($deserialized->__getPublicMethod());
		$this->assertNull($deserialized->__getProtectedMethod());
		$this->assertNull($deserialized->__getPrivateMethod());
	}

	public function testOnlyNonPublicGettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::NON_PUBLIC_GETTERS);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@type' => DifferentVisibilityClass::class,
			'protectedMethod' => '5678',
			'privateMethod' => '9876',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->__getPublicProperty());
		$this->assertNull($deserialized->__getProtectedProperty());
		$this->assertNull($deserialized->__getPrivateProperty());
		$this->assertNull($deserialized->__getPublicMethod());
		$this->assertNull($deserialized->__getProtectedMethod());
		$this->assertNull($deserialized->__getPrivateMethod());
	}

	public function testOnlyPublicSettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::PUBLIC_SETTERS);
		$input = $this->createInput();

		$serialized = $serializer->serialize($input);
		$this->assertEquals('null', $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize(json_encode([
			'@type' => DifferentVisibilityClass::class,
			'publicMethod' => '1234',
		]));

		$this->assertNull($deserialized->__getPublicProperty());
		$this->assertNull($deserialized->__getProtectedProperty());
		$this->assertNull($deserialized->__getPrivateProperty());
		$this->assertEquals('1234', $deserialized->__getPublicMethod());
		$this->assertNull($deserialized->__getProtectedMethod());
		$this->assertNull($deserialized->__getPrivateMethod());
	}

	public function testOnlyNonPublicSettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::NON_PUBLIC_SETTERS);
		$input = $this->createInput();

		$serialized = $serializer->serialize($input);
		$this->assertEquals('null', $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize(json_encode([
			'@type' => DifferentVisibilityClass::class,
			'protectedMethod' => '5678',
			'privateMethod' => '9876',
		]));

		$this->assertNull($deserialized->__getPublicProperty());
		$this->assertNull($deserialized->__getProtectedProperty());
		$this->assertNull($deserialized->__getPrivateProperty());
		$this->assertNull($deserialized->__getPublicMethod());
		$this->assertEquals('5678', $deserialized->__getProtectedMethod());
		$this->assertEquals('9876', $deserialized->__getPrivateMethod());
	}

	public function testOnlyGettersAndSettersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL_GETTERS_SETTERS);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@type' => DifferentVisibilityClass::class,
			'publicMethod' => '1234',
			'protectedMethod' => '5678',
			'privateMethod' => '9876',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->__getPublicProperty());
		$this->assertNull($deserialized->__getProtectedProperty());
		$this->assertNull($deserialized->__getPrivateProperty());
		$this->assertEquals('1234', $deserialized->__getPublicMethod());
		$this->assertEquals('5678', $deserialized->__getProtectedMethod());
		$this->assertEquals('9876', $deserialized->__getPrivateMethod());
	}

	public function testOnlyPublicMembersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL_PUBLIC);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@type' => DifferentVisibilityClass::class,
			'publicProperty' => 'foo',
			'publicMethod' => '1234',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertEquals('foo', $deserialized->__getPublicProperty());
		$this->assertNull($deserialized->__getProtectedProperty());
		$this->assertNull($deserialized->__getPrivateProperty());
		$this->assertEquals('1234', $deserialized->__getPublicMethod());
		$this->assertNull($deserialized->__getProtectedMethod());
		$this->assertNull($deserialized->__getPrivateMethod());
	}

	public function testOnlyNonPublicMembersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL_NON_PUBLIC);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@type' => DifferentVisibilityClass::class,
			'protectedProperty' => 'bar',
			'privateProperty' => 'baz',
			'protectedMethod' => '5678',
			'privateMethod' => '9876',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);

		$this->assertNull($deserialized->__getPublicProperty());
		$this->assertEquals('bar', $deserialized->__getProtectedProperty());
		$this->assertEquals('baz', $deserialized->__getPrivateProperty());
		$this->assertNull($deserialized->__getPublicMethod());
		$this->assertEquals('5678', $deserialized->__getProtectedMethod());
		$this->assertEquals('9876', $deserialized->__getPrivateMethod());
	}

	public function testAllMembersStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::ALL);
		$input = $this->createInput();

		$expectedOutput = json_encode([
			'@type' => DifferentVisibilityClass::class,
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

		$this->assertEquals('foo', $deserialized->__getPublicProperty());
		$this->assertEquals('bar', $deserialized->__getProtectedProperty());
		$this->assertEquals('baz', $deserialized->__getPrivateProperty());
		$this->assertEquals('1234', $deserialized->__getPublicMethod());
		$this->assertEquals('5678', $deserialized->__getProtectedMethod());
		$this->assertEquals('9876', $deserialized->__getPrivateMethod());
	}

	public function testNoneStrategy()
	{
		$serializer = $this->createSerializer(IncludeStrategy::NONE);
		$input = $this->createInput();

		$serialized = $serializer->serialize($input);
		$this->assertEquals('null', $serialized);

		/** @var DifferentVisibilityClass $deserialized */
		$deserialized = $serializer->deserialize("{}", DifferentVisibilityClass::class);

		$this->assertNull($deserialized->__getPublicProperty());
		$this->assertNull($deserialized->__getProtectedProperty());
		$this->assertNull($deserialized->__getPrivateProperty());
		$this->assertNull($deserialized->__getPublicMethod());
		$this->assertNull($deserialized->__getProtectedMethod());
		$this->assertNull($deserialized->__getPrivateMethod());
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
	protected abstract function createSerializer($strategy);
}
