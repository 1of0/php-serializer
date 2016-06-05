<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use DateTime;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Mappers\FactoryChainFactory;
use OneOfZero\Json\Mappers\Reflection\ReflectionFactory;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingConverters;
use OneOfZero\Json\Test\FixtureClasses\Converters\ClassDependentMemberConverter;
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

		$this->defaultConfiguration = new Configuration();
		$this->defaultConfiguration->getMetaHintWhitelist()->allowClassesInNamespace('OneOfZero\\Json\\Test\\FixtureClasses');
		
		$this->serializer = new Serializer($this->defaultConfiguration);
		$this->serializer->setChainFactory((new FactoryChainFactory)->withAddedFactory(new ReflectionFactory()));
		
	}

	public function testTypeConverters()
	{
		$date = new DateTime();
		
		$this->serializer->getConfiguration()->getConverters()->addForTypes(
			ClassDependentMemberConverter::class, 
			[ SimpleClass::class, ReferableClass::class ]
		);

		$expectedJson = json_encode([
			'@class'                => ClassUsingConverters::class,
			'dateObject'            => $date->getTimestamp(),
			'simpleClass'           => '1234|abcd|5678',
			'referableClass'        => 1337,
			'foo'                   => 123,
			'bar'                   => 123,
			'contextSensitive'      => 2,
		]);

		$object = new ClassUsingConverters();
		$object->dateObject         = $date;
		$object->simpleClass        = new SimpleClass('1234', 'abcd', '5678');
		$object->referableClass     = new ReferableClass(1337);
		$object->foo                = 123;
		$object->bar                = 123;
		$object->contextSensitive   = 2;
		$object->setPrivateDateObject($date);

		$json = $this->serializer->serialize($object);
		$this->assertEquals($expectedJson, $json);

		/** @var ClassUsingConverters $deserialized */
		$deserialized = $this->serializer->deserialize($json);

		$this->assertObjectEquals($object, $deserialized);
	}
}
