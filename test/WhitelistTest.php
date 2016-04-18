<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\AlternativeSimpleClass;
use OneOfZero\Json\Test\FixtureClasses\ClassImplementingTypeHintableInterface;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClassExtender;
use OneOfZero\Json\Test\FixtureClasses\SubNamespace\SimpleClassInSubNamespace;
use OneOfZero\Json\Test\FixtureClasses\TypeHintableInterface;
use stdClass;

class WhitelistTest extends AbstractTest
{
	public function testEmptyWhitelist()
	{
		$serializer = new Serializer();
		
		$object = new SimpleClass('1234', 'abcd', 'efgh');
		
		$deserialized = $serializer->deserialize($serializer->serialize($object));
		$this->assertInstanceOf(stdClass::class, $deserialized);
	}

	public function testWhitelistedClass()
	{
		$serializer = new Serializer();

		$serializer->getConfiguration()->metaHintWhitelist->allowClass(SimpleClass::class);
		
		$simpleClass = new SimpleClass('1234', 'abcd', 'efgh');
		$altClass = new AlternativeSimpleClass('1234', 'abcd', 'efgh');

		// SimpleClass should be whitelisted
		$deserialized = $serializer->deserialize($serializer->serialize($simpleClass));
		$this->assertInstanceOf(SimpleClass::class, $deserialized);

		// AlternativeSimpleClass should not
		$deserialized = $serializer->deserialize($serializer->serialize($altClass));
		$this->assertInstanceOf(stdClass::class, $deserialized);
	}

	public function testWhitelistedSuperclass()
	{
		$serializer = new Serializer();

		$serializer->getConfiguration()->metaHintWhitelist->allowClass(SimpleClass::class);
		
		$simpleClass = new SimpleClass('1234', 'abcd', 'efgh');
		$extender = new SimpleClassExtender('1234', 'abcd', 'efgh', '5678');

		// SimpleClass should be whitelisted
		$deserialized = $serializer->deserialize($serializer->serialize($simpleClass));
		$this->assertInstanceOf(SimpleClass::class, $deserialized);

		// SimpleClassExtender should be whitelisted too
		$deserialized = $serializer->deserialize($serializer->serialize($extender));
		$this->assertInstanceOf(SimpleClassExtender::class, $deserialized);
	}
	
	public function testWhitelistedInterface()
	{
		$serializer = new Serializer();
		$serializer->getConfiguration()->metaHintWhitelist->allowClassesImplementing(TypeHintableInterface::class);

		$simpleClass = new SimpleClass('1234', 'abcd', 'efgh');
		$typeHintableImplementing = new ClassImplementingTypeHintableInterface('1234', 'abcd', 'efgh');

		// SimpleClass should not be whitelisted
		$deserialized = $serializer->deserialize($serializer->serialize($simpleClass));
		$this->assertInstanceOf(stdClass::class, $deserialized);
		
		// ClassImplementingTypeHintableInterface should
		$deserialized = $serializer->deserialize($serializer->serialize($typeHintableImplementing));
		$this->assertInstanceOf(ClassImplementingTypeHintableInterface::class, $deserialized);
	}
	
	public function testWhitelistedNamespace()
	{
		$serializer = new Serializer();
		$serializer->getConfiguration()->metaHintWhitelist->allowClassesInNamespace('OneOfZero\Json\Test\FixtureClasses\SubNamespace');

		$simpleClass = new SimpleClass('1234', 'abcd', 'efgh');
		$simpleClassInSubNamespace = new SimpleClassInSubNamespace('1234', 'abcd', 'efgh');

		// SimpleClass should not be whitelisted
		$deserialized = $serializer->deserialize($serializer->serialize($simpleClass));
		$this->assertInstanceOf(stdClass::class, $deserialized);

		// SimpleClassInSubNamespace should
		$deserialized = $serializer->deserialize($serializer->serialize($simpleClassInSubNamespace));
		$this->assertInstanceOf(SimpleClassInSubNamespace::class, $deserialized);
	}
	
	public function testWhitelistedPattern()
	{
		$serializer = new Serializer();
		$serializer->getConfiguration()->metaHintWhitelist->allowClassesMatchingPattern('/^OneOfZero\\\\Json\\\\Test\\\\FixtureClasses\\\\Simple/');

		$simpleClass = new SimpleClass('1234', 'abcd', 'efgh');
		$altClass = new AlternativeSimpleClass('1234', 'abcd', 'efgh');

		// SimpleClass should be whitelisted
		$deserialized = $serializer->deserialize($serializer->serialize($simpleClass));
		$this->assertInstanceOf(SimpleClass::class, $deserialized);

		// AlternativeSimpleClass should not
		$deserialized = $serializer->deserialize($serializer->serialize($altClass));
		$this->assertInstanceOf(stdClass::class, $deserialized);
	}
	
	public function testNonDeserializeExistingClass()
	{
		$serializer = new Serializer();

		$input = json_encode([
			'@class'    => 'NonExistingClass',
			'foo'       => 'bar',
		]);

		// This test is just for coverage
		
		$deserialized = $serializer->deserialize($input);
		$this->assertInstanceOf(stdClass::class, $deserialized);
	}
}
