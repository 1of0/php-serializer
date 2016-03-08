<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Internals\ProxyHelper;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\ClassReferencingArray;
use OneOfZero\Json\Test\FixtureClasses\ClassReferencingReferableClass;
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use OneOfZero\Json\Test\FixtureClasses\ReferableClassResolver;
use OneOfZero\Json\Test\FixtureClasses\ClassWithLazyReference;
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

		$serializer = new Serializer();
		$serializer->setReferenceResolver(new ReferableClassResolver());

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

	public function testMultipleReferences()
	{
		$expectedJson = json_encode([
			'@class' => ClassReferencingArray::class,
			'references' => [
				[ '@class' => ReferableClass::class, 'id' => 1 ],
				[ '@class' => ReferableClass::class, 'id' => 2 ],
				[ '@class' => ReferableClass::class, 'id' => 3 ]
			]
		]);

		$object = new ClassReferencingArray();
		$object->references = [
			new ReferableClass(1),
			new ReferableClass(2),
			new ReferableClass(3)
		];

		$serializer = new Serializer();
		$serializer->setReferenceResolver(new ReferableClassResolver());

		$json = $serializer->serialize($object);
		$this->assertEquals($expectedJson, $json);

		/** @var ClassReferencingArray $deserialized */
		$deserialized = $serializer->deserialize($json);
		$this->assertNotNull($deserialized);
		for ($i = 0; $i < 3; $i++)
		{
			$this->assertEquals($object->references[$i]->getId(), $deserialized->references[$i]->getId());
			$this->assertEquals($object->references[$i]->getIdDouble(), $deserialized->references[$i]->getIdDouble());
		}
	}
	
	public function testLazyReference()
	{
		$resolver = new ReferableClassResolver();
		$proxyHelper = new ProxyHelper($resolver);

		$expectedJson = json_encode([
			'@class' => ClassWithLazyReference::class,
			'reference' => [
				'@class' => ReferableClass::class,
				'id' => 9001
			]
		]);

		$object = new ClassWithLazyReference();
		$object->reference = new ReferableClass(9001);

		$serializer = new Serializer();
		$serializer->setReferenceResolver($resolver);

		$json = $serializer->serialize($object);
		$this->assertEquals($expectedJson, $json);

		/** @var ClassReferencingReferableClass $deserialized */
		$deserialized = $serializer->deserialize($json);
		$this->assertNotNull($deserialized);
		$this->assertTrue($proxyHelper->isProxy($deserialized->reference));
		//$this->assertEquals($object->reference->getId(), $deserialized->reference->getId());

		// Serializing a proxy
		$json = $serializer->serialize($deserialized);
		$this->assertEquals($expectedJson, $json);
	}
}
