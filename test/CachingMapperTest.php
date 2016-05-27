<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use Doctrine\Common\Cache\ArrayCache;
use OneOfZero\Json\Mappers\Caching\CachingMapperFactory;
use OneOfZero\Json\Mappers\File\PhpArrayMapperFactory;
use OneOfZero\Json\Mappers\MapperPipeline;
use OneOfZero\Json\Mappers\Reflection\ReflectionMapperFactory;

class CachingMapperTest extends AbstractMapperTest
{
	const PHP_ARRAY_MAPPING_FILE = __DIR__ . '/Assets/mapping.php';
	
	const EXPECTED_CACHE_STATS = [
		[ 1, 2 ],
		[ 4, 3 ],
		[ 7, 4 ],
		[ 10, 5 ],
		[ 13, 6 ],
		[ 13, 8 ],
		[ 13, 10 ],
		[ 13, 12 ],
		[ 13, 14 ],
	];

	/**
	 * @var CachingMapperFactory $cachingFactory
	 */
	private static $cachingMapper;

	/**
	 * @var int $testCounter
	 */
	private static $testCounter = 0;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();
		self::$cachingMapper = new CachingMapperFactory(new ArrayCache());
	}
	
	public function assertPostConditions()
	{
		parent::assertPostConditions();
		$this->runTest();
		
		$expectedStats = self::EXPECTED_CACHE_STATS[self::$testCounter++];
		$actualStats = self::$cachingMapper->getCache()->getStats();
		
		$this->assertEquals($expectedStats[0], $actualStats['hits']);
		$this->assertEquals($expectedStats[1], $actualStats['misses']);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getPipeline()
	{
		return (new MapperPipeline)
			->withFactory(self::$cachingMapper)
			->withFactory(new PhpArrayMapperFactory(self::PHP_ARRAY_MAPPING_FILE))
			->withFactory(new ReflectionMapperFactory())
			->build($this->defaultConfiguration)
		;
	}
}
