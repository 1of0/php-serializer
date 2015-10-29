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
use RuntimeException;

class Environment
{
	/**
	 * @var AnnotationReader $annotationReader
	 */
	private static $annotationReader;

	/**
	 * @return string
	 */
	public static function getVendorPath()
	{
		$options = [
			realpath($_SERVER['DOCUMENT_ROOT'] . '/vendor'),
			realpath(__DIR__ . '/../../../../../../vendor'),
			realpath(__DIR__ . '/../../../vendor'),
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
	 * @return AnnotationReader
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