<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use DateTime;
use OneOfZero\Json\JsonConvert;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingClassLevelCustomConverter;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingCustomConverters;
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;

class CustomConverterTests extends AbstractTest
{
	public function testCustomConverters()
	{
		$date = new DateTime();

		$expectedJson = json_encode([
			'@class' => ClassUsingCustomConverters::class,
			'dateObject' => $date->getTimestamp(),
			'simpleClass' => '1234|abcd',
			'referableClass' => 1337,
			'foo' => 877,
			'bar' => 1123,
			'contextSensitive' => 1337 * 2,
			'privateDateObject' => $date->getTimestamp()
		]);

		$object = new ClassUsingCustomConverters();
		$object->dateObject = $date;
		$object->simpleClass = new SimpleClass('1234', 'abcd');
		$object->referableClass = new ReferableClass(1337);
		$object->foo = 123;
		$object->bar = 123;
		$object->contextSensitive = 2;
		$object->setPrivateDateObject($date);

		$json = JsonConvert::toJson($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = JsonConvert::fromJson($json);
		$this->assertObjectEquals($object, $deserialized);
	}

	public function testClassLevelCustomConverter()
	{
		$object = new ClassUsingClassLevelCustomConverter();
		$object->foo = 1234;

		$expectedJson = json_encode([
			'abc' => 1234,
			'@class' => ClassUsingClassLevelCustomConverter::class
		]);

		$json = JsonConvert::toJson($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = JsonConvert::fromJson($json);
		$this->assertObjectEquals($object, $deserialized);
	}
}
