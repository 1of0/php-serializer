<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test;

use OneOfZero\Json\Configuration;
use OneOfZero\Json\Enums\OnRecursion;
use OneOfZero\Json\Exceptions\RecursionException;
use OneOfZero\Json\Serializer;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;

class RecursionTest extends AbstractTest
{
	public function testRecursionExpectException()
	{
		$this->markTestSkipped('Needs to be fixed');
		return;
		
		$this->setExpectedException(RecursionException::class);
		
		$config = new Configuration();
		$config->defaultRecursionHandlingStrategy = OnRecursion::THROW_EXCEPTION;

		$input = new SimpleClass(new SimpleClass(null, null, null), null, null);
		$input->foo->bar = $input;
		
		$serializer = new Serializer($config);
		$serializer->serialize($input);
	}
}
