<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\JsonConvert;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\PrivatePropertiesClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClassExtender;
use stdClass;

class BasicFunctionalityTest extends AbstractTest
{
	public function testNumericArray()
	{
		$expectedJson = '[1,2,3,4]';
		$array = [ 1, 2, 3, 4 ];

		$json = Serializer::get()->serialize($array);
		$this->assertEquals($expectedJson, $json);

		$deserialized = Serializer::get()->deserialize($json);
		$this->assertSame($array, $deserialized);
	}

	public function testSimpleObject()
	{
		$expectedJson = json_encode([
			'@class' => SimpleClass::class,
			'foo' => '1234',
			'bar' => 'abcd'
		]);

		$object = new SimpleClass('1234', 'abcd');

		$json = Serializer::get()->serialize($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = Serializer::get()->deserialize($json);
		$this->assertObjectEquals($object, $deserialized);
	}

	public function testPrivatePropertiesObject()
	{
		$expectedJson = json_encode([
			'@class' => PrivatePropertiesClass::class,
			'foo' => '1234'
		]);

		$object = new PrivatePropertiesClass('1234', 'abcd');

		$json = Serializer::get()->serialize($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = Serializer::get()->deserialize($json);
		$this->assertEquals($object->getFoo(), $deserialized->getFoo());
		$this->assertNull($deserialized->getBar());
	}

	public function testStdClass()
	{
		$object = new stdClass();
		$object->foo = '1234';
		$object->bar = 'abcd';

		$expectedJson = json_encode($object);

		$json = Serializer::get()->serialize($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = Serializer::get()->deserialize($json);
		$this->assertObjectEquals($object, $deserialized);
	}

	public function testObjectArray()
	{
		$expectedObject = [
			'@class' => SimpleClass::class,
			'foo' => '1234',
			'bar' => 'abcd'
		];
		$expectedJson = json_encode([ $expectedObject, $expectedObject ]);

		$object = new SimpleClass('1234', 'abcd');
		$array = [ $object, $object ];

		$json = Serializer::get()->serialize($array);
		$this->assertEquals($expectedJson, $json);

		$deserialized = Serializer::get()->deserialize($json);
		$this->assertSequenceEquals($array, $deserialized);
	}

	public function testCast()
	{
		$object = new SimpleClassExtender('1234', 'abcd', '1337');
		$expected = new SimpleClass('1234', 'abcd');

		$cast = JsonConvert::cast($object, SimpleClass::class);
		$this->assertObjectEquals($expected, $cast);
		$this->assertEquals(SimpleClass::class, get_class($cast));
	}
}
