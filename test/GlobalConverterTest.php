<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Mappers\FactoryChainFactory;
use OneOfZero\Json\Mappers\Reflection\ReflectionFactory;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\ClassReferencingReferableClass;
use OneOfZero\Json\Test\FixtureClasses\Converters\GlobalMemberTypeConverter;
use OneOfZero\Json\Test\FixtureClasses\Converters\GlobalObjectTypeConverter;
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;

class GlobalConverterTest extends AbstractTest
{
	/**
	 * @var Serializer $serializer
	 */
	private $serializer;
	
	public function setUp()
	{
		parent::setUp();

		$this->configuration = new Configuration();
		$this->configuration->getMetaHintWhitelist()->allowClassesInNamespace('OneOfZero\\Json\\Test\\FixtureClasses');
		
		$this->serializer = new Serializer($this->configuration);
		$this->serializer->setChainFactory((new FactoryChainFactory)->withAddedFactory(new ReflectionFactory()));
	}

	public function testObjectTypeConverter()
	{
		$converters = $this->serializer->getConfiguration()->getConverters();
		
		$converters->addForType(GlobalObjectTypeConverter::class, SimpleClass::class);

		$object = new SimpleClass('foo', 'bar', 'baz');
		$expectedJson = json_encode(serialize($object));

		$json = $this->serializer->serialize($object);
		$this->assertEquals($expectedJson, $json);
		
		$deserializedObject = $this->serializer->deserialize($json, SimpleClass::class);
		$this->assertObjectEquals($object, $deserializedObject);
	}

	public function testMemberTypeConverter()
	{
		$converters = $this->serializer->getConfiguration()->getConverters();
		
		$converters->addForType(GlobalMemberTypeConverter::class, ReferableClass::class);

		$object = new ClassReferencingReferableClass();
		$object->reference = new ReferableClass(1234);
		$expectedJson = json_encode([
			'@type' => ClassReferencingReferableClass::class,
			'reference' => base64_encode(serialize($object->reference)),
		]);

		$json = $this->serializer->serialize($object);
		$this->assertEquals($expectedJson, $json);
		
		$deserializedObject = $this->serializer->deserialize($json);
		$this->assertObjectEquals($object, $deserializedObject);
	}

	public function testObjectConverter()
	{
		$converters = $this->serializer->getConfiguration()->getConverters();
		
		$converters->add(GlobalObjectTypeConverter::class);

		$object = new SimpleClass('foo', 'bar', 'baz');
		$expectedJson = json_encode(serialize($object));

		$json = $this->serializer->serialize($object);
		$this->assertEquals($expectedJson, $json);
		
		$deserializedObject = $this->serializer->deserialize($json, SimpleClass::class);
		$this->assertObjectEquals($object, $deserializedObject);
	}

	public function testMemberConverter()
	{
		$converters = $this->serializer->getConfiguration()->getConverters();
		
		$converters->add(GlobalMemberTypeConverter::class);

		$object = new ClassReferencingReferableClass();
		$object->reference = new ReferableClass(1234);
		$expectedJson = json_encode([
			'@type' => ClassReferencingReferableClass::class,
			'foo' => base64_encode(serialize(null)),
			'bar' => base64_encode(serialize(null)),
			'reference' => base64_encode(serialize($object->reference)),
		]);

		$json = $this->serializer->serialize($object);
		$this->assertEquals($expectedJson, $json);
		
		$deserializedObject = $this->serializer->deserialize($json);
		$this->assertObjectEquals($object, $deserializedObject);
	}
}
