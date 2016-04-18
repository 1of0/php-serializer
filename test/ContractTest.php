<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\ContractResolvers\PropertyNameContractResolver;
use OneOfZero\Json\Exceptions\NotSupportedException;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClassExtender;
use stdClass;

class ContractTest extends AbstractTest
{
	public function testPascalCaseContract()
	{
		$serializer = new Serializer($this->defaultConfiguration);
		$serializer->getConfiguration()->contractResolver = new PropertyNameContractResolver();

		$input = new SimpleClassExtender('abcd', '1234', 'efgh', '5678');

		$expectedOutput = json_encode([
			'@class'            => SimpleClassExtender::class,
			'ExtensionProperty' => '5678',
			'Foo'               => 'abcd',
			'Bar'               => '1234',
			'Baz'               => 'efgh',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		$deserialized = $serializer->deserialize($serialized);
		$this->assertObjectEquals($input, $deserialized);
	}
	
	public function testAnonymousObjectContract()
	{
		$serializer = new Serializer($this->defaultConfiguration);
		$serializer->getConfiguration()->contractResolver = new PropertyNameContractResolver();

		$input = new stdClass();
		$input->foo                 = 'abcd';
		$input->bar                 = '1234';
		$input->baz                 = 'efgh';
		$input->extensionProperty   = '5678';

		$expectedOutput = json_encode([
			'Foo'               => 'abcd',
			'Bar'               => '1234',
			'Baz'               => 'efgh',
			'ExtensionProperty' => '5678',
		]);

		$serialized = $serializer->serialize($input);
		$this->assertEquals($expectedOutput, $serialized);

		$deserialized = $serializer->deserialize($serialized);
		$this->assertObjectEquals($input, $deserialized);
	}
	
	public function testInvalidContractConfiguration()
	{
		$serializer = new Serializer();
		$serializer->getConfiguration()->contractResolver = new SimpleClass(null, null, null);

		$this->setExpectedException(NotSupportedException::class);
		$serializer->serialize(new SimpleClass('abcd', '1234', 'efgh'));
	}
}
