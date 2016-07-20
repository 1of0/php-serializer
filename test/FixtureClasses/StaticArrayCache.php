<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

class StaticArrayCache extends CacheProvider
{
	/**
	 * @var array[] $data each element being a tuple of [$data, $expiration], where the expiration is int|bool
	 */
	private static $data = [];

	/**
	 * @var int
	 */
	private static $hitsCount = 0;

	/**
	 * @var int
	 */
	private static $missesCount = 0;

	/**
	 * @var int
	 */
	private static $upTime;
	
	public static function __constructStatic()
	{
		self::resetCache();
	}
	
	public static function resetCache()
	{
		self::$data = [];
		self::$hitsCount = 0;
		self::$missesCount = 0;
		self::$upTime = time();
	}

	/**
	 * @return array
	 */
	public static function getStatsStatic()
	{
		return
		[
			Cache::STATS_HITS             => self::$hitsCount,
			Cache::STATS_MISSES           => self::$missesCount,
			Cache::STATS_UPTIME           => self::$upTime,
			Cache::STATS_MEMORY_USAGE     => null,
			Cache::STATS_MEMORY_AVAILABLE => null,
		];
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function doFetch($id)
	{
		$namespaceKey = sprintf(self::DOCTRINE_NAMESPACE_CACHEKEY, $this->getNamespace());
		
		if (!$this->doContains($id))
		{
			if ($id !== $namespaceKey)
			{
				self::$missesCount += 1;
			}
			return false;
		}

		self::$hitsCount += 1;

		return self::$data[$id][0];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doContains($id)
	{
		if (!isset(self::$data[$id]))
		{
			return false;
		}

		$expiration = self::$data[$id][1];

		if ($expiration && $expiration < time())
		{
			$this->doDelete($id);
			return false;
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doSave($id, $data, $lifeTime = 0)
	{
		self::$data[$id] = [$data, $lifeTime ? time() + $lifeTime : false];

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doDelete($id)
	{
		unset(self::$data[$id]);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doFlush()
	{
		self::$data = [];

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doGetStats()
	{
		return self::getStatsStatic();
	}
}

StaticArrayCache::__constructStatic();
