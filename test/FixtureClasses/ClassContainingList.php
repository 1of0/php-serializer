<?php
/**
 * Copyright (c) 2016 Bernardo van der Wal
 * MIT License
 *
 * Refer to the LICENSE file for the full copyright notice.
 */

namespace OneOfZero\Json\Test\FixtureClasses;

use OneOfZero\Json\Annotations\IsArray;
use OneOfZero\Json\Annotations\Type;

class ClassContainingList
{
    /**
     * @IsArray
     * @Type(SimpleClass::class)
     * @var SimpleClass[] $items
     */
    public $items;
}
