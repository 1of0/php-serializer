<?php


namespace OneOfZero\Json;


interface RepositoryInterface
{
	/**
	 * @param mixed $id
	 * @return mixed
	 */
	public static function get($id);
}