<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use DateTime;
use OneOfZero\Json\Helpers\Metadata;
use OneOfZero\Json\Mappers\MapperPipeline;
use OneOfZero\Json\Mappers\ReflectionMapperFactory;
use OneOfZero\Json\Mappers\YamlMapperFactory;
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use OneOfZero\Json\Test\FixtureClasses\UnmappedClass;
use OneOfZero\Json\Test\FixtureClasses\UnmappedClassUsingClassLevelConverter;
use OneOfZero\Json\Test\FixtureClasses\UnmappedClassUsingConverters;
use OneOfZero\Json\Test\FixtureClasses\UnmappedClassUsingDifferentClassLevelConverters;
use OneOfZero\Json\Visitors\DeserializingVisitor;
use OneOfZero\Json\Visitors\SerializingVisitor;

class YamlVisitorTest extends AbstractTest
{
	const YAML_MAPPING_FILE = __DIR__ . '/Assets/mapping.yaml';

	public function testSerialization()
	{
		$input = new UnmappedClass('abc', '123', 'def');

		$expectedOutput = [
			Metadata::TYPE => UnmappedClass::class,
			'food' => 'abc',
			'bar' => '123',
		];

		$output = $this->createSerializingVisitor()->visit($input);
		$this->assertSequenceEquals($expectedOutput, $output);
	}

	public function testConverters()
	{
		$date = new DateTime();
		
		$input = new UnmappedClassUsingConverters();
		$input->dateObject          = $date;
		$input->simpleClass         = new SimpleClass('1234', 'abcd');
		$input->referableClass      = new ReferableClass(1337);
		$input->foo                 = 123;
		$input->bar                 = 123;
		$input->contextSensitive    = 2;
		$input->setPrivateDateObject($date);

		$expectedOutput = [
			'@class'                => UnmappedClassUsingConverters::class,
			'dateObject'            => $date->getTimestamp(),
			'simpleClass'           => '1234|abcd',
			'referableClass'        => 1337,
			'foo'                   => 877,
			'bar'                   => 1123,
			'contextSensitive'      => 1337 * 2,
			'differentConverters'   => 'foo',
			'privateDateObject'     => $date->getTimestamp(),
		];

		$serialized = $this->createSerializingVisitor()->visit($input);
		$this->assertSequenceEquals($expectedOutput, $serialized);
		
		/** @var UnmappedClassUsingConverters $deserialized */
		$deserialized = $this->createDeserializingVisitor()->visit((object)$serialized);
		
		$this->assertEquals('bar', $deserialized->differentConverters);
		$deserialized->differentConverters = null;
		
		$this->assertObjectEquals($input, $deserialized);
	}
	
	public function testClassLevelConverter()
	{
		$object = new UnmappedClassUsingClassLevelConverter();
		$object->foo = 1234;

		$expectedOutput = [
			'@class'    => UnmappedClassUsingClassLevelConverter::class,
			'abcd'       => 1234,
		];

		$serialized = $this->createSerializingVisitor()->visit($object);
		$this->assertSequenceEquals($expectedOutput, $serialized);

		$deserialized = $this->createDeserializingVisitor()->visit((object)$serialized);
		$this->assertObjectEquals($object, $deserialized);
	}
	
	public function testDifferentClassLevelConverters()
	{
		$object = new UnmappedClassUsingDifferentClassLevelConverters();

		$expectedOutput = [
			'@class'    => UnmappedClassUsingDifferentClassLevelConverters::class,
			'abcd'       => 1234,
		];

		$serialized = $this->createSerializingVisitor()->visit($object);
		$this->assertSequenceEquals($expectedOutput, $serialized);

		/** @var UnmappedClassUsingDifferentClassLevelConverters $deserialized */
		$deserialized = $this->createDeserializingVisitor()->visit((object)$serialized);
		
		$this->assertEquals('bar', $deserialized->foo);
		$deserialized->foo = null;
		
		$this->assertObjectEquals($object, $deserialized);
	}

	private function createSerializingVisitor()
	{
		$pipeline = (new MapperPipeline)
			->addFactory(new YamlMapperFactory(self::YAML_MAPPING_FILE))
			->addFactory(new ReflectionMapperFactory())
			->build($this->defaultConfiguration)
		;
		return new SerializingVisitor(clone $this->defaultConfiguration, $pipeline);
	}

	private function createDeserializingVisitor()
	{
		$pipeline = (new MapperPipeline)
			->addFactory(new YamlMapperFactory(self::YAML_MAPPING_FILE))
			->addFactory(new ReflectionMapperFactory())
			->build($this->defaultConfiguration)
		;
		return new DeserializingVisitor(clone $this->defaultConfiguration, $pipeline);
	}
}
