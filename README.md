# 1of0/json

This library provides advanced serialization features built over the PHP `json_encode()` and `json_decode()` functions. 
Most of the features are inspired by the popular .NET library [Json.NET](http://www.newtonsoft.com/json).

## Installation

This library is available on [Packagist](https://packagist.org/packages/1of0/json), and installable with composer:

```shell
composer require 1of0/json
```

## Quick start

The most straightforward way of using this library is using the static methods on the `Convert` class. The `Convert`
class is a static facade around the singleton instance of the Serializer class.


```php
<?php

use OneOfZero\Json\Convert;
use OneOfZero\Json\Serializer;

// Basic serialization
$json = Convert::toJson($myObject);

// Basic deserialization
$object = Convert::fromJson($json);

// Type hint example
$object = Convert::fromJson($json, \MyNamespace\MyClass::class);

// This is a more verbose form of Convert::toJson($myObject)
$json = Serializer::get()->serialize($myObject);
```

## How does it work?

The serializer takes annotated or XML/YAML/JSON/PHP mapped objects, and pre-processes them before feeding them to the
`json_encode()` function. Inversely, the deserializer feeds the JSON to the `json_decode()` function, and 
post-processes the result to get as close a match to the original object (assuming it's properly mapped/annotated).

## Features

### Mappers and mapper chaining

The serializer and deserializer behaviour can be influenced with mappings on classes and class members. This library
supports mappings with:

- Annotations
- XML
- YAML
- JSON
- PHP

These mappers can be chained in any order to provide a single merged mapping.

### Embedded type information

This is a feature inspired by the [zumba/json-serializer](https://github.com/zumba/json-serializer) library.

By default the serializer will embed type information in serialized objects. The type information allows the
deserializer to deserialize a JSON object into its original type without PHPDoc, annotations or mappings.


The embedded data is the additional `@type` property that holds the fully qualified class name of the serialized
object:
```json
{
	"@type": "MyNamespace\\MyClass",
	"propertyA": "valueA",
	"propertyB": "valueB",
	...
}
```

##### Serialization

The embedding of type information can be disabled for individual classes with class-level mappings, or can disabled
globally in the configuration:
```php
<?php
$configuration->embedTypeMetadata = false;
```

##### Deserialization

Due to security concerns the deserializer only deserializes whitelisted types (by default this whitelist is empty).
Classes can be added to the whitelist through the configuration:
```php
<?php
$configuration->getMetaHintWhitelist()->allowClass(MyClass::class);
```
You can also whitelist by namespace, class inheritance, or regex. See the API documentation of the `MetaHintWhiteList`
class for details.

### Object and member converters

Much like [Json.NET's custom converters](http://www.newtonsoft.com/json/help/html/CustomJsonConverter.htm), this library
also allows you to build and specify custom converters for specified properties.

### Contract resolver

Another feature that was loosely ported from Json.NET is the 
[contract resolver](http://www.newtonsoft.com/json/help/html/CustomContractResolver.htm). A contract resolver allows you
to dynamically manipulate the serialization mapping for every node in the (de)serialization tree. An example use-case 
for a contract resolver is conversion of property names.

### Serialization groups

***(not implemented yet)***

### Reference properties

You might often deal with sub-objects that you don't want to serialize, but rather just reference. This library can
serialize and deserialize references like that. To achieve this:

- The referenced object needs to implement the `ReferableInterface` interface
- The property that holds the referenced object has to be marked with the `@IsReference` annotation
- A reference resolver needs to exist that supports the referenced object (and needs to implement the 
  `ReferenceResolverInterface` interface)

## Bugs and feature requests

Please post any bugs and feature requests to the issue tracker on
[Bitbucket](https://bitbucket.org/1of0/json/issues?status=new&status=open).

Also don't hesitate to file an issue if the documentation is lacking.

## License

The library is licensed under the MIT license, of which the full text can be found in the [LICENSE](LICENSE) file.
