<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use DateTime;
use OneOfZero\Json\JsonConvert;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingClassLevelConverter;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingConverters;
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;

class CustomConverterTests extends AbstractTest
{
	public function testCustomConverters()
	{
		$date = new DateTime();

		$expectedJson = json_encode([
			'@class'            => ClassUsingConverters::class,
			'dateObject'        => $date->getTimestamp(),
			'simpleClass'       => '1234|abcd',
			'referableClass'    => 1337,
			'foo'               => 877,
			'bar'               => 1123,
			'contextSensitive'  => 1337 * 2,
			'privateDateObject' => $date->getTimestamp(),
		]);

		$object = new ClassUsingConverters();
		$object->dateObject         = $date;
		$object->simpleClass        = new SimpleClass('1234', 'abcd');
		$object->referableClass     = new ReferableClass(1337);
		$object->foo                = 123;
		$object->bar                = 123;
		$object->contextSensitive   = 2;
		$object->setPrivateDateObject($date);

		$json = JsonConvert::toJson($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = JsonConvert::fromJson($json);
		$this->assertObjectEquals($object, $deserialized);
	}

	public function testClassLevelCustomConverter()
	{
		$object = new ClassUsingClassLevelConverter();
		$object->foo = 1234;

		$expectedJson = json_encode([
			'@class'    => ClassUsingClassLevelConverter::class,
			'abc'       => 1234,
		]);

		$json = JsonConvert::toJson($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = JsonConvert::fromJson($json);
		$this->assertObjectEquals($object, $deserialized);
	}
}
