<?php


namespace OneOfZero\Json;


interface CacheInterface
{
	public function store($key, $value);

	public function get();
}