<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Mappers\Caching;

use Doctrine\Common\Cache\CacheProvider;
use OneOfZero\Json\Mappers\SourceInterface;

class CacheSource implements SourceInterface
{
	/**
	 * @var CacheProvider $cache
	 */
	private $cache;

	/**
	 * @param CacheProvider $cache
	 */
	public function __construct(CacheProvider $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * @return CacheProvider
	 */
	public function getCache()
	{
		return $this->cache;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHash()
	{
		return sha1(__CLASS__ . $this->cache->getNamespace());
	}
}
