<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use Exception;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Internals\Environment;
use OneOfZero\Json\Internals\Mappers\AnnotationMapperFactory;
use OneOfZero\Json\Internals\Mappers\MapperFactoryInterface;
use OneOfZero\Json\Internals\Mappers\MapperPipeline;
use OneOfZero\Json\Internals\Mappers\ReflectionMapperFactory;
use OneOfZero\Json\Test\FixtureClasses\EqualityInterface;
use PHPUnit_Framework_TestCase;

abstract class AbstractTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Configuration $defaultConfiguration
	 */
	protected $defaultConfiguration;

	/**
	 * @var MapperFactoryInterface $defaultPipeline
	 */
	protected $defaultPipeline;

	/**
	 *
	 */
	protected function setUp()
	{
		$this->defaultConfiguration = new Configuration();
		$this->defaultPipeline = (new MapperPipeline)
			->addFactory(new AnnotationMapperFactory(Environment::getAnnotationReader()))
			->addFactory(new ReflectionMapperFactory())
			->build($this->defaultConfiguration)
		;
	}


	/**
	 * @param $expected
	 * @param $actual
	 * @throws Exception
	 */
	protected function assertSequenceEquals($expected, $actual)
	{
		if (!is_array($expected))
		{
			throw new Exception("Expected value is not a sequence");
		}

		foreach ($expected as $key => $value)
		{
			$this->assertTrue(array_key_exists($key, $actual), "Missing item with key $key in the actual sequence");

			if (is_array($value))
			{
				$this->assertSequenceEquals($value, $actual[$key]);
			}

			if (is_object($value))
			{
				$this->assertObjectEquals($value, $actual[$key]);
			}

			$this->assertEquals($value, $actual[$key]);
		}
	}

	/**
	 * @param $expected
	 * @param $actual
	 * @throws Exception
	 */
	protected function assertObjectEquals($expected, $actual)
	{
		if (is_null($expected))
		{
			$this->assertNull($actual);
			return;
		}

		$this->assertNotNull($actual);

		if ($expected instanceof EqualityInterface)
		{
			$this->assertTrue($expected->__equals($actual));
			return;
		}

		$this->assertInstanceOf(get_class($expected), $actual);

		foreach ($expected as $property => $value)
		{
			$this->assertEquals($value, $actual->{$property});
		}
	}
}
