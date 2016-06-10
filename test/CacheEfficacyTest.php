<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;
use OneOfZero\Json\Test\FixtureClasses\StaticArrayCache;

class CacheEfficacyTest extends AbstractTest
{
	/**
	 * @var Serializer $serializer
	 */
	private $serializer;

	/**
	 * @var SimpleClass $instanceFixture
	 */
	private $instanceFixture;

	/**
	 * @var string $jsonFixture
	 */
	private $jsonFixture;
	
	public function setUp()
	{
		parent::setUp();
				
		$this->serializer = new Serializer($this->configuration);
		$this->serializer->setCacheProvider(new StaticArrayCache());
		
		$this->instanceFixture = new SimpleClass('abcd', 1234, true);
		$this->jsonFixture = json_encode([
			'@type'    => SimpleClass::class,
			'food'      => 'abcd',
			'bar'       => 1234,
		]);
		
		StaticArrayCache::resetCache();
	}
	
	public function testSingleRunSerializer()
	{
		$this->serializer->serialize($this->instanceFixture);
		
		$this->assertEquals(0, $this->getHits());
		$this->assertEquals(4, $this->getMisses());
	}
	
	public function testMultiRunSerializer()
	{
		for ($i = 0; $i < 10; $i++)
		{
			$this->serializer->serialize($this->instanceFixture);
		}
		
		$this->assertEquals(36, $this->getHits());
		$this->assertEquals(4, $this->getMisses());
	}
	
	public function testSingleRunDeserializer()
	{
		$this->serializer->deserialize($this->jsonFixture);
		
		$this->assertEquals(0, $this->getHits());
		$this->assertEquals(4, $this->getMisses());
	}
	
	public function testMultiRunDeserializer()
	{
		for ($i = 0; $i < 10; $i++)
		{
			$this->serializer->deserialize($this->jsonFixture);
		}
		
		$this->assertEquals(36, $this->getHits());
		$this->assertEquals(4, $this->getMisses());
	}
	
	private function getHits()
	{
		return StaticArrayCache::getStatsStatic()['hits'];
	}
	
	private function getMisses()
	{
		return StaticArrayCache::getStatsStatic()['misses'];
	}
}
