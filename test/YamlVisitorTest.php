<?php

namespace OneOfZero\Json\Test;

use DateTime;
use OneOfZero\Json\Internals\Mappers\MapperPipeline;
use OneOfZero\Json\Internals\Mappers\ReflectionMapperFactory;
use OneOfZero\Json\Internals\Mappers\YamlMapperFactory;
use OneOfZero\Json\Internals\Metadata;
use OneOfZero\Json\Internals\Visitors\SerializingVisitor;
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use OneOfZero\Json\Test\FixtureClasses\UnmappedClass;
use OneOfZero\Json\Test\FixtureClasses\UnmappedClassUsingConverters;

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

		$output = $this->createVisitor()->visit($input);
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
			'@class'            => UnmappedClassUsingConverters::class,
			'dateObject'        => $date->getTimestamp(),
			'simpleClass'       => '1234|abcd',
			'referableClass'    => 1337,
			'foo'               => 877,
			'bar'               => 1123,
			'contextSensitive'  => 1337 * 2,
			'privateDateObject' => $date->getTimestamp(),
		];

		$output = $this->createVisitor()->visit($input);
		$this->assertSequenceEquals($expectedOutput, $output);
	}

	private function createVisitor()
	{
		$pipeline = (new MapperPipeline)
			->addFactory(new YamlMapperFactory(self::YAML_MAPPING_FILE))
			->addFactory(new ReflectionMapperFactory())
			->build($this->defaultConfiguration)
		;
		return new SerializingVisitor(clone $this->defaultConfiguration, $pipeline);
	}
}
