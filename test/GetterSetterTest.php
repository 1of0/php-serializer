<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use Doctrine\Common\Annotations\AnnotationException;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\ClassWithGetterAndSetter;
use OneOfZero\Json\Test\FixtureClasses\ClassWithGetterAndSetterOnProperty;
use OneOfZero\Json\Test\FixtureClasses\ClassWithInvalidGetterAndSetter;

class GetterSetterTest extends AbstractTest
{
	public function testGetterAndSetter()
	{
		$expectedJson = json_encode([
			'@type'    => ClassWithGetterAndSetter::class,
			'foo'       => '1234'
		]);

		$object = new ClassWithGetterAndSetter('1234');

		$json = Serializer::get()->serialize($object);
		$this->assertEquals($expectedJson, $json);

		/** @var ClassWithGetterAndSetter $deserialized */
		$deserialized = Serializer::get()->deserialize($json);
		$this->assertEquals($object->getFoo(), $deserialized->getFoo());
	}

	public function testInvalidGetterParameters()
	{
		$this->setExpectedException(SerializationException::class);
		Serializer::get()->serialize(new ClassWithInvalidGetterAndSetter());
	}

	public function testInvalidSetterParameters()
	{
		$this->setExpectedException(SerializationException::class);
		/** @var ClassWithInvalidGetterAndSetter $deserialized */
		Serializer::get()->deserialize(json_encode([
			'@type'    => ClassWithInvalidGetterAndSetter::class,
			'foo'       => '1234',
		]));
	}

	public function testInvalidGetterDefinition()
	{
		$this->setExpectedException(AnnotationException::class);
		Serializer::get()->serialize(new ClassWithGetterAndSetterOnProperty());
	}

	public function testInvalidSetterDefinition()
	{
		$this->setExpectedException(AnnotationException::class);
		/** @var ClassWithInvalidGetterAndSetter $deserialized */
		Serializer::get()->deserialize(json_encode([
			'@type'    => ClassWithGetterAndSetterOnProperty::class,
			'foo'       => '1234',
			'bar'       => '5678',
		]));
	}
}
