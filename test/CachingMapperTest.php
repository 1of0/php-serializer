<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use Doctrine\Common\Cache\ArrayCache;
use OneOfZero\Json\Configuration;
use OneOfZero\Json\Mappers\AbstractArray\ArrayFactory;
use OneOfZero\Json\Mappers\FactoryChain;
use OneOfZero\Json\Mappers\FactoryChainFactory;
use OneOfZero\Json\Mappers\File\PhpFileSource;
use OneOfZero\Json\Mappers\Reflection\ReflectionFactory;

class CachingMapperTest// extends AbstractMapperTest
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
	 * @var FactoryChain $chain
	 */
	private static $chain;

	/**
	 * @var int $testCounter
	 */
	private static $testCounter = 0;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();
		
		$configuration = new Configuration(null, false);		
		$configuration->metaHintWhitelist->allowClassesInNamespace('OneOfZero\\Json\\Test\\FixtureClasses');
		
		self::$chain = (new FactoryChainFactory)
			->setCache(new ArrayCache())
			->addFactory(new ArrayFactory(new PhpFileSource(self::PHP_ARRAY_MAPPING_FILE)))
			->addFactory(new ReflectionFactory())
			->build($configuration)
		;
	}
	
	public function assertPostConditions()
	{
		/*parent::assertPostConditions();
		$this->runTest();
		
		$expectedStats = self::EXPECTED_CACHE_STATS[self::$testCounter++];
		$actualStats = self::$chain->get()->getStats();
		
		$this->assertEquals($expectedStats[0], $actualStats['hits']);
		$this->assertEquals($expectedStats[1], $actualStats['misses']);*/
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getChain()
	{
		return self::$chain;
	}
}
