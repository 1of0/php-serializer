<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\ContractResolvers\PropertyNameContractResolver;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\SimpleClassExtender;
use stdClass;

class ContractTest extends AbstractTest
{
	public function testPascalCaseContract()
	{
		$config = new Configuration();
		$config->contractResolver = new PropertyNameContractResolver();
		$serializer = new Serializer($config);

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
		$config = new Configuration();
		$config->contractResolver = new PropertyNameContractResolver();
		$serializer = new Serializer($config);

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
}
