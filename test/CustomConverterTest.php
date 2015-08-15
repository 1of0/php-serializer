<?php


namespace OneOfZero\Json\Test;


use DateTime;
use OneOfZero\Json\JsonConvert;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingCustomConverters;
use OneOfZero\Json\Test\FixtureClasses\SimpleObject;
use PHPUnit_Framework_TestCase;

class CustomConverterTests extends PHPUnit_Framework_TestCase
{
	public function testCustomConverters()
	{
		$date = new DateTime();

		$expectedJson = json_encode([
			'@class' => 'OneOfZero\\Json\\Test\\FixtureClasses\\ClassUsingCustomConverters',
			'dateObject' => $date->getTimestamp(),
			'variableObject' => '1234',
			'foo' => 877,
			'bar' => 1123
		]);

		$object = new ClassUsingCustomConverters();
		$object->dateObject = $date;
		$object->variableObject = new SimpleObject('1234', 'abcd');
		$object->foo = 123;
		$object->bar = 123;

		$json = JsonConvert::toJson($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = JsonConvert::fromJson($json);
		//var_dump($deserialized);
	}
}