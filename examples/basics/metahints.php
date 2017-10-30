<?php

namespace OneOfZero\Json\Examples;

include_once(__DIR__ . '/../bootstrap.php');

use OneOfZero\Json\Serializer;

class Foo
{
	public $bar;
	public $baz;
}

$foo = new Foo();
$foo->bar = '1234';
$foo->baz = true;

$serializer = new Serializer();

$json = $serializer->serialize($foo);
var_dump($json);
// string(66) "{"@type":"OneOfZero\\Json\\Examples\\Foo","bar":"1234","baz":true}"

// By default (for security reasons), meta-hinting in json is ignored (the @type property)
$deserializedDefaultHintingConfiguration = $serializer->deserialize($json);
var_dump(get_class($deserializedDefaultHintingConfiguration));
// string(8) "stdClass"

// With default settings you must statically hint the type during deserialization
$deserializedStaticHinting = $serializer->deserialize($json, Foo::class);
var_dump(get_class($deserializedStaticHinting));
// string(27) "OneOfZero\Json\Examples\Foo"

// In the configuration you can allow the class Foo to be meta-hinted in json for deserialization
$serializer->getConfiguration()->getMetaHintWhitelist()->allowClass(Foo::class);
//$serializer->getConfiguration()->getMetaHintWhitelist()->allowClassesImplementing(SomeInterface::class);
//$serializer->getConfiguration()->getMetaHintWhitelist()->allowClassesInNamespace('Some\Namespace');
//$serializer->getConfiguration()->getMetaHintWhitelist()->allowClassesMatchingPattern('/^Some\\Namespace\\.*/');

// With Foo whitelisted, you don't have to statically hint during deserialization anymore
$deserializedWhitelistedHinting = $serializer->deserialize($json);
var_dump(get_class($deserializedWhitelistedHinting));
// string(27) "OneOfZero\Json\Examples\Foo"