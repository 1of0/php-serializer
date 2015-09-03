<?php


namespace OneOfZero\Json\Test;


use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\ClassReferencingReferableClass;
use OneOfZero\Json\Test\FixtureClasses\FakeContainerAdapter;
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use PHPUnit_Framework_TestCase;

class ReferencePropertyTest extends PHPUnit_Framework_TestCase
{
	public function testReference()
	{
		$expectedJson = json_encode([
			'@class' => ClassReferencingReferableClass::class,
			'foo' => 'String value',
			'bar' => 1.337,
			'reference' => [
				'@class' => ReferableClass::class,
				'id' => 9001
			]
		]);

		$object = new ClassReferencingReferableClass();
		$object->foo = "String value";
		$object->bar = 1.337;
		$object->reference = new ReferableClass(9001);

		$serializer = new Serializer(new FakeContainerAdapter());

		$json = $serializer->serialize($object);
		$this->assertEquals($expectedJson, $json);

		/** @var ClassReferencingReferableClass $deserialized */
		$deserialized = $serializer->deserialize($json);
		$this->assertNotNull($deserialized);
		$this->assertEquals($object->foo, $deserialized->foo);
		$this->assertEquals($object->bar, $deserialized->bar);
		$this->assertEquals($object->reference->getId(), $deserialized->reference->getId());
		$this->assertEquals($object->reference->getIdDouble(), $deserialized->reference->getIdDouble());
	}
}