<?php

/**
 * Copyright (c) 2015 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use DateTime;
use OneOfZero\Json\Annotations\Getter;
use OneOfZero\Json\Annotations\Setter;
use OneOfZero\Json\Annotations\Type;
use OneOfZero\Json\Annotations\Converter;
use /** @noinspection PhpUnusedAliasInspection */OneOfZero\Json\Converters\DateTimeConverter;

class ClassUsingConverters
{
	/**
	 * @var DateTime $privateDateObject
	 */
	private $privateDateObject;

	/**
	 * @Converter(DateTimeConverter::class)
	 * @var DateTime $dateObject
	 */
	public $dateObject;

	/**
	 * @Converter(ClassDependentMemberConverter::class)
	 * @var SimpleClass $simpleClass
	 */
	public $simpleClass;

	/**
	 * @Converter(ClassDependentMemberConverter::class)
	 * @var ReferableClass $referableClass
	 */
	public $referableClass;

	/**
	 * @Converter(PropertyDependentMemberConverter::class)
	 * @var int $foo
	 */
	public $foo;

	/**
	 * @Converter(PropertyDependentMemberConverter::class)
	 * @var int $bar
	 */
	public $bar;

	/**
	 * @Converter(ContextSensitiveMemberConverter::class)
	 * @var int $contextSensitive
	 */
	public $contextSensitive;

	/**
	 * @Getter
	 * @Converter(DateTimeConverter::class)
	 *
	 * @return DateTime
	 */
	public function getPrivateDateObject()
	{
		return $this->privateDateObject;
	}

	/**
	 * @Setter
	 * @Converter(DateTimeConverter::class)
	 * 
	 * @param DateTime $dateObject
	 */
	public function setPrivateDateObject(DateTime $dateObject)
	{
		$this->privateDateObject = $dateObject;
	}
}
