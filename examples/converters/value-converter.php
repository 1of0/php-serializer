<?php

namespace OneOfZero\Json\Examples;

include_once(__DIR__ . '/../bootstrap.php');

use OneOfZero\Json\Annotations\Converter;
use OneOfZero\Json\Convert;
use OneOfZero\Json\Converters\AbstractMemberConverter;
use OneOfZero\Json\Nodes\MemberNode;

class Foo
{
	/**
	 * @Converter(Base64Converter::class)
	 */
	public $bar;

	/**
	 * @Converter(Base64Converter::class)
	 */
	public $baz;
}

class Base64Converter extends AbstractMemberConverter
{
	/**
	 * {@inheritdoc}
	 */
	public function serialize(MemberNode $node, $typeHint = null)
	{
		// When serializing use $node->getValue() to get the value of the member that is being serialized
		if (is_string($node->getValue()))
		{
			return base64_encode($node->getValue());
		}
		else
		{
			return $node->getValue();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize(MemberNode $node, $typeHint = null)
	{
		// When deserializing use $node->getSerializedValue() to get the serialized value of the member
		if (is_string($node->getSerializedValue()))
		{
			return base64_decode($node->getSerializedValue());
		}
		else
		{
			return $node->getSerializedValue();
		}
	}

}

$foo = new Foo();
$foo->bar = '1234';
$foo->baz = true;

$json = Convert::toJson($foo);
var_dump($json);
// string(70) "{"@type":"OneOfZero\\Json\\Examples\\Foo","bar":"MTIzNA==","baz":true}"

$deserialized = Convert::fromJson($json, Foo::class);
var_dump($deserialized);
// class OneOfZero\Json\Examples\Foo#59 (2) {
//   public $bar =>
//   string(4) "1234"
//   public $baz =>
//   bool(true)
// }

