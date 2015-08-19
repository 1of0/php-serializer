<?php


namespace OneOfZero\Json\Test;


use Exception;
use OneOfZero\Json\Test\FixtureClasses\EqualityInterface;
use PHPUnit_Framework_TestCase;
use stdClass;

abstract class AbstractTest extends PHPUnit_Framework_TestCase
{
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
