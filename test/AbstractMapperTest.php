<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use DateTime;
use OneOfZero\Json\Exceptions\SerializationException;
use OneOfZero\Json\Helpers\Metadata;
use OneOfZero\Json\Mappers\FactoryInterface;
use OneOfZero\Json\Test\FixtureClasses\ClassWithGetterAndSetter;
use OneOfZero\Json\Test\FixtureClasses\ClassWithGetterAndSetterOnProperty;
use OneOfZero\Json\Test\FixtureClasses\ClassWithInvalidGetterAndSetter;
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingClassLevelConverter;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingConverters;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingDifferentClassLevelConverters;
use OneOfZero\Json\Visitors\DeserializingVisitor;
use OneOfZero\Json\Visitors\SerializingVisitor;

abstract class AbstractMapperTest extends AbstractTest
{
	/**
	 * @return FactoryInterface
	 */
	protected abstract function getChain();

	public function testSerialization()
	{
		$input = new SimpleClass('abc', '123', 'def');

		$expectedOutput = [
			Metadata::TYPE => SimpleClass::class,
			'food' => 'abc',
			'bar' => '123',
		];

		$output = $this->createSerializingVisitor()->visit($input);
		$this->assertSequenceEquals($expectedOutput, $output);
	}

	public function testConverters()
	{
		$date = new DateTime();
		
		$input = new ClassUsingConverters();
		$input->dateObject          = $date;
		$input->simpleClass         = new SimpleClass('1234', 'abcd', '5678');
		$input->referableClass      = new ReferableClass(1337);
		$input->foo                 = 123;
		$input->bar                 = 123;
		$input->contextSensitive    = 2;
		$input->setPrivateDateObject($date);

		$expectedOutput = [
			'@class'                => ClassUsingConverters::class,
			'dateObject'            => $date->getTimestamp(),
			'simpleClass'           => '1234|abcd|5678',
			'referableClass'        => 1337,
			'foo'                   => 877,
			'bar'                   => 1123,
			'contextSensitive'      => 1337 * 2,
			'differentConverters'   => 'foo',
			'privateDateObject'     => $date->getTimestamp(),
		];

		$serialized = $this->createSerializingVisitor()->visit($input);
		$this->assertSequenceEquals($expectedOutput, $serialized);
		
		/** @var ClassUsingConverters $deserialized */
		$deserialized = $this->createDeserializingVisitor()->visit((object)$serialized);
		
		$this->assertEquals('bar', $deserialized->differentConverters);
		$deserialized->differentConverters = null;
		
		$this->assertObjectEquals($input, $deserialized);
	}
	
	public function testClassLevelConverter()
	{
		$object = new ClassUsingClassLevelConverter();
		$object->foo = 1234;

		$expectedOutput = [
			'@class'    => ClassUsingClassLevelConverter::class,
			'abcd'      => 1234,
		];

		$serialized = $this->createSerializingVisitor()->visit($object);
		$this->assertSequenceEquals($expectedOutput, $serialized);

		$deserialized = $this->createDeserializingVisitor()->visit((object)$serialized);
		$this->assertObjectEquals($object, $deserialized);
	}
	
	public function testDifferentClassLevelConverters()
	{
		$object = new ClassUsingDifferentClassLevelConverters();

		$expectedOutput = [
			'@class'    => ClassUsingDifferentClassLevelConverters::class,
			'abcd'      => 1234,
		];

		$serialized = $this->createSerializingVisitor()->visit($object);
		$this->assertSequenceEquals($expectedOutput, $serialized);

		/** @var ClassUsingDifferentClassLevelConverters $deserialized */
		$deserialized = $this->createDeserializingVisitor()->visit((object)$serialized);
		
		$this->assertEquals('bar', $deserialized->foo);
		$deserialized->foo = null;
		
		$this->assertObjectEquals($object, $deserialized);
	}
	
	public function testGetterSetter()
	{
		$object = new ClassWithGetterAndSetter('bar');

		$expectedOutput = [
			'@class'    => ClassWithGetterAndSetter::class,
			'foo'       => 'bar',
		];
		
		$serialized = $this->createSerializingVisitor()->visit($object);
		$this->assertSequenceEquals($expectedOutput, $serialized);

		$deserialized = $this->createDeserializingVisitor()->visit((object)$serialized);
		$this->assertObjectEquals($object, $deserialized);
	}
	
	public function testInvalidGetter()
	{
		$this->setExpectedException(SerializationException::class);
		$this->createSerializingVisitor()->visit(new ClassWithInvalidGetterAndSetter('bar'));
	}

	public function testInvalidSetter()
	{
		$this->setExpectedException(SerializationException::class);

		$input = (object)[
			'@class'    => ClassWithInvalidGetterAndSetter::class,
			'foo'       => 'bar',
		];
		
		$this->createDeserializingVisitor()->visit($input);
	}
	
	public function testGetterOnProperty()
	{
		$this->setExpectedException(SerializationException::class);
		$this->createSerializingVisitor()->visit(new ClassWithGetterAndSetterOnProperty('bar'));
	}
	
	public function testSetterOnProperty()
	{
		$this->setExpectedException(SerializationException::class);
		
		$input = (object)[
			'@class'    => ClassWithInvalidGetterAndSetter::class,
			'foo'       => 'bar',
		];
		
		$this->createDeserializingVisitor()->visit($input);
	}
	
	private function createSerializingVisitor()
	{
		return new SerializingVisitor(clone $this->defaultConfiguration, $this->getChain());
	}

	private function createDeserializingVisitor()
	{
		return new DeserializingVisitor(clone $this->defaultConfiguration, $this->getChain());
	}
}
