<?php


namespace OneOfZero\Json\Test;


use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\NoMetaDataSpecifyingClass;
use PHPUnit_Framework_TestCase;

class NoMetaDataTest extends PHPUnit_Framework_TestCase
{
	public function testNoMetaData()
	{
		$arrayObject = [
			'foo' => '1234',
			'bar' => 'abcd'
		];
		$expectedJson = json_encode($arrayObject);

		$object = new NoMetaDataSpecifyingClass('1234', 'abcd');

		$json = Serializer::get()->serialize($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = Serializer::get()->deserialize($json);
		$this->assertSame($arrayObject, $deserialized);
	}
}