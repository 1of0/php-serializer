<?php

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Internals\Mappers\MapperPipeline;
use OneOfZero\Json\Internals\Mappers\ReflectionMapperFactory;
use OneOfZero\Json\Internals\Mappers\YamlMapperFactory;
use OneOfZero\Json\Internals\Metadata;
use OneOfZero\Json\Internals\Visitors\SerializingVisitor;
use OneOfZero\Json\Test\FixtureClasses\YamlMappedClass;

class YamlVisitorTest extends AbstractTest
{
	const YAML_MAPPING_FILE = __DIR__ . '/Assets/mapping.yaml';

	public function testSerialization()
	{
		$input = new YamlMappedClass('abc', '123', 'def');

		$expectedOutput = [
			Metadata::TYPE => YamlMappedClass::class,
			'food' => 'abc',
			'bar' => '123',
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
