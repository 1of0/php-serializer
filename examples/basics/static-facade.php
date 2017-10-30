<?php

namespace OneOfZero\Json\Examples;

include_once(__DIR__ . '/../bootstrap.php');

use OneOfZero\Json\Convert;

class Foo
{
	public $bar;
	public $baz;
}

class Bar extends Foo
{
}

$foo = new Foo();
$foo->bar = '1234';
$foo->baz = true;

$json = Convert::toJson($foo);
var_dump($json);
// string(66) "{"@type":"OneOfZero\\Json\\Examples\\Foo","bar":"1234","baz":true}"

$deserialized = Convert::fromJson($json, Foo::class);
var_dump($deserialized);
// class OneOfZero\Json\Examples\Foo#57 (2) {
//   public $bar =>
//   string(4) "1234"
//   public $baz =>
//   bool(true)
// }

$casted = Convert::cast($deserialized, Bar::class);
var_dump($casted);
// class OneOfZero\Json\Examples\Bar#104 (2) {
//   public $bar =>
//   string(4) "1234"
//   public $baz =>
//   bool(true)
// }
