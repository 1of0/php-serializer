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
	const CACHE_NAMESPACE = '1of0_json_mapper';
	
	/**
	 * @var CacheProvider $cache
	 */
	private $cache;

	/**
	 * @param CacheProvider $cache
	 */
	public function __construct(CacheProvider $cache)
	{
		$this->cache = clone $cache;
		$this->cache->setNamespace(self::CACHE_NAMESPACE);
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
