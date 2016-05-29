<?php

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
}
