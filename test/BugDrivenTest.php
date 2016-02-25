<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingStaticCustomConverter;

class BugDrivenTest extends AbstractTest
{
	/**
	 * Test for issue #3
	 * @see https://bitbucket.org/1of0/json/issues/3
	 */
	public function testIssue003()
	{
		$expectedJson = json_encode([
			'@class' => ClassUsingStaticCustomConverter::class,
			'someProperty' => 'foo'
		]);
		$object = new ClassUsingStaticCustomConverter();

		$json = Serializer::get()->serialize($object);

		$this->assertNull($object->someProperty);
		$this->assertEquals($expectedJson, $json);

		$json = '{}';
		$object = Serializer::get()->deserialize($json, ClassUsingStaticCustomConverter::class);
		$this->assertInstanceOf(ClassUsingStaticCustomConverter::class, $object);
		$this->assertEquals('bar', $object->someProperty);
	}
}