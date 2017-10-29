<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use DateTime;
use Exception;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Helpers\Environment;
use OneOfZero\Json\Mappers\Annotation\AnnotationFactory;
use OneOfZero\Json\Mappers\Annotation\AnnotationSource;
use OneOfZero\Json\Mappers\FactoryChain;
use OneOfZero\Json\Mappers\FactoryChainFactory;
use OneOfZero\Json\Mappers\Reflection\ReflectionFactory;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\EqualityInterface;
use PHPUnit_Framework_TestCase;

abstract class AbstractTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Configuration $configuration
	 */
	protected $configuration;

	/**
	 * @var FactoryChain $factoryChain
	 */
	protected $factoryChain;

	/**
	 *
	 */
	protected function setUp()
	{
		$this->configuration = new Configuration(null, false);

		$this->factoryChain = (new FactoryChainFactory)
			->withAddedFactory(new AnnotationFactory(new AnnotationSource(Environment::getAnnotationReader())))
			->withAddedFactory(new ReflectionFactory())
			->build($this->configuration)
		;

		$this->configuration->getMetaHintWhitelist()->allowClassesInNamespace('OneOfZero\\Json\\Test\\FixtureClasses');

		Serializer::get()->setConfiguration($this->configuration);
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
		
		$this->assertNotNull($actual, "Actual sequence is null");

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

		foreach ($actual as $key => $value)
		{
			$this->assertTrue(array_key_exists($key, $expected), "Item with key $key is not expected");
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

		foreach ($expected as $property => $expectedValue)
		{
			$actualValue = $actual->{$property};

			if ($expectedValue instanceof DateTime && $actualValue instanceof DateTime)
			{
				$this->assertEquals($expectedValue->getTimestamp(), $actualValue->getTimestamp());
				$this->assertEquals($expectedValue->getTimezone(), $actualValue->getTimezone());
			}
			else
			{
				$this->assertEquals($expectedValue, $actual->{$property});
			}
		}
	}
}
