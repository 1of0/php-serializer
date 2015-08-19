<?php


namespace OneOfZero\Json\Test;


use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use OneOfZero\Json\Test\Traits\AssertObjectEqualsTrait;
use OneOfZero\Json\Test\Traits\AssertSequenceEqualsTrait;
use PHPUnit_Framework_TestCase;

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
			'@class' => 'OneOfZero\\Json\\Test\\FixtureClasses\\SimpleClass',
			'foo' => '1234',
			'bar' => 'abcd'
		]);

		$object = new SimpleClass('1234', 'abcd');

		$json = Serializer::get()->serialize($object);
		$this->assertEquals($expectedJson, $json);

		$deserialized = Serializer::get()->deserialize($json);

		$this->assertObjectEquals($object, $deserialized);
		/*$this->assertEquals($object->foo, $deserialized->foo);
		$this->assertEquals($object->bar, $deserialized->bar);*/
	}

	public function testObjectArray()
	{
		$expectedObject = [
			'@class' => 'OneOfZero\\Json\\Test\\FixtureClasses\\SimpleClass',
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
		/*$this->assertEquals($array[0]->foo, $deserialized[0]->foo);
		$this->assertEquals($array[0]->bar, $deserialized[0]->bar);
		$this->assertEquals($array[1]->foo, $deserialized[1]->foo);
		$this->assertEquals($array[1]->bar, $deserialized[1]->bar);*/
	}
}