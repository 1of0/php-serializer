<?php


namespace OneOfZero\Json\Test;


use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\NoMetadataSpecifyingClass;

class NoMetadataTest extends AbstractTest
{
	public function testNoMetadata()
	{
		$arrayObject = [
			'foo' => '1234',
			'bar' => 'abcd'
		];
		$expectedJson = json_encode($arrayObject);

		$object = new NoMetadataSpecifyingClass('1234', 'abcd');

		$json = Serializer::get()->serialize($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = Serializer::get()->deserialize($json);
		$this->assertObjectEquals((object)$arrayObject, $deserialized);
	}
}