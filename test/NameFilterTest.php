<?php

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\LongPropertyNamesClass;
use OneOfZero\Json\Test\FixtureClasses\PascalCaseNameFilter;

class NameFilterTest extends AbstractTest
{
	public function testNameFilter()
	{
		$config = new Configuration();
		$config->nameFilterClass = PascalCaseNameFilter::class;
		
		$serializer = new Serializer(null, $config);
		
		$input = new LongPropertyNamesClass();
		$input->firstPropertyName = 'abcd';
		$input->secondPropertyName = '1234';
		$input->setAnExampleMethodName('foo');

		$expectedOutput = json_encode([
			'@class' => LongPropertyNamesClass::class,
			'FirstPropertyName' => 'abcd',
			'SecondPropertyName' => '1234',
			'AnExampleMethodName' => 'foo',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		/** @var LongPropertyNamesClass $deserialized */
		$deserialized = $serializer->deserialize($serialized);
		$this->assertEquals($input->firstPropertyName, $deserialized->firstPropertyName);
		$this->assertEquals($input->secondPropertyName, $deserialized->secondPropertyName);
		$this->assertEquals($input->getAnExampleMethodName(), $deserialized->getAnExampleMethodName());
	}
}