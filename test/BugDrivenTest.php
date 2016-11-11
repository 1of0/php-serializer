<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\ClassContainingAnnotatedStdClass;
use OneOfZero\Json\Test\FixtureClasses\ClassContainingList;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingStaticCustomConverter;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use stdClass;

class BugDrivenTest extends AbstractTest
{
	/**
	 * Test for issue #3
	 * @see https://gitlab.com/1of0/php-serializer/issues/3
	 */
	public function testIssue003()
	{
		$expectedJson = json_encode([
			'@type' => ClassUsingStaticCustomConverter::class,
			'someProperty' => 'foo'
		]);
		$object = new ClassUsingStaticCustomConverter();

		$json = Serializer::get()->serialize($object);

		$this->assertNull($object->someProperty);
		$this->assertEquals($expectedJson, $json);

		$json = '{}';
		$object = Serializer::get()->deserialize($json, ClassUsingStaticCustomConverter::class);
		$this->assertInstanceOf(ClassUsingStaticCustomConverter::class, $object);
		$this->assertEquals('bar', $object->someProperty);
	}

	/**
	 * Test for issue #16
	 * @see https://gitlab.com/1of0/php-serializer/issues/16
	 */
	public function testIssue016()
	{
		$expectedJson = json_encode([
			'@type' => ClassContainingList::class,
			'items' => [
				[
					'@type'    => SimpleClass::class,
					'foo'      => 'a',
					'bar'      => 'a',
					'baz'      => 'a',
				],
				[
					'@type'    => SimpleClass::class,
					'foo'      => 'b',
					'bar'      => 'b',
					'baz'      => 'b',
				]
			],
		]);
		$object = new ClassContainingList();
		$object->items = [ new SimpleClass('a', 'a', 'a'), new SimpleClass('b', 'b', 'b') ];

		$json = Serializer::get()->serialize($object);

		$this->assertEquals($expectedJson, $json);

		$object = Serializer::get()->deserialize($json, ClassContainingList::class);
		$this->assertInstanceOf(ClassContainingList::class, $object);
		$this->assertEquals(2, count($object->items));
		$this->assertInstanceOf(SimpleClass::class, $object->items[0]);
		$this->assertInstanceOf(SimpleClass::class, $object->items[1]);
	}

	/**
	 * Test for issue #17
	 * @see https://gitlab.com/1of0/php-serializer/issues/17
	 */
	public function testIssue017()
	{
		$expectedJson = json_encode([
			'@type' => ClassContainingAnnotatedStdClass::class,
			'foo' => [
				'bar' => 'baz'
			],
		]);


		$object = new ClassContainingAnnotatedStdClass();
		$object->foo = new stdClass();
		$object->foo->bar = 'baz';

		$json = Serializer::get()->serialize($object);

		$this->assertEquals($expectedJson, $json);

		$object = Serializer::get()->deserialize($json, ClassContainingAnnotatedStdClass::class);
		$this->assertInstanceOf(ClassContainingAnnotatedStdClass::class, $object);
		$this->assertInstanceOf(stdClass::class, $object->foo);
		$this->assertEquals('baz', $object->foo->bar);
	}
}
