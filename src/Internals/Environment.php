<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Internals;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use RuntimeException;

class Environment
{
	/**
	 * @var Reader $annotationReader
	 */
	private static $annotationReader;

	/**
	 * @return string
	 */
	public static function getVendorPath()
	{
		$options = [
			__DIR__ . '/../../../../../vendor',
			__DIR__ . '/../../../../vendor',
			__DIR__ . '/../../../vendor',
			__DIR__ . '/../../vendor',
			$_SERVER['DOCUMENT_ROOT'] . '/vendor',
			$_SERVER['DOCUMENT_ROOT'] . '/../vendor',
		];

		foreach ($options as $option)
		{
			if (file_exists($option))
			{
				return $option;
			}
		}
		throw new RuntimeException('Could not determine vendor directory');
	}

	/**
	 * @return string
	 */
	public static function getAutoloadFile()
	{
		$autoloader = self::getVendorPath() . '/autoload.php';

		if (!file_exists($autoloader))
		{
			throw new RuntimeException('Could not locate autoload.php');
		}

		return $autoloader;
	}

	/**
	 * @return Reader
	 */
	public static function getAnnotationReader()
	{
		if (!self::$annotationReader)
		{
			/** @noinspection PhpIncludeInspection */
			AnnotationRegistry::registerLoader([ require(self::getAutoloadFile()), 'loadClass' ]);
			self::$annotationReader = new AnnotationReader();
		}
		return self::$annotationReader;
	}
}