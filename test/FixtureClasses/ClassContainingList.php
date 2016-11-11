<?php
/**
 * Created by PhpStorm.
 * User: bvanderwal
 * Date: 11-11-2016
 * Time: 10:21
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
