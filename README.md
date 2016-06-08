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

// The same as Convert::toJson($myObject)
$json = Serializer::get()->serialize($myObject);
```

## How does it work?

The serializer takes annotated or XML/YAML/JSON mapped objects, and pre-processes them before feeding them to the 
`json_encode()` function. Inversely, the deserializer feeds the JSON to the `json_decode()` function, and 
post-processes the result to get as close a match to the original object (assuming it's properly mapped/annotated).

Behaviour is mostly defined through the annotations; see the [documentation](documentation.md) page for a reference. 

## Features

### Serialized type information

When an object is serialized, by default, a metadata property will be appended with the object's class name so that it 
can be properly deserialized by this library (this feature was inspired by the 
[zumba/json-serializer](https://github.com/zumba/json-serializer) library).

```json
{
	"@class": "MyNamespace\\MyClass",
	"propertyA": "valueA",
	"propertyB": "valueB",
	...
}
```

This feature can be disabled with the `@NoMetadata` annotation, if for instance a receiving side is very strict with the
input. For most purposes this shouldn't be harmful though.

Additionally, the deserialization methods also allow you to provide an optional type hint, if there is no metadata
available.

### Custom property converters

Much like [Json.NET's custom converters](http://www.newtonsoft.com/json/help/html/CustomJsonConverter.htm), this library
also allows you to build and specify custom converters for specified properties. Refer to the
`CustomMemberConverterInterface` interface, `CustomObjectConverterInterface` interface and `@Converter` annotation
in the [documentation](documentation.md) for this feature.

### Reference properties

You might often deal with sub-objects that you don't want to serialize, but rather just reference. This library can
serialize and deserialize references like that. To achieve this:

- The referenced object needs to implement the `ReferableInterface` interface
- The property that holds the referenced object has to be marked with the `@IsReference` annotation
- A reference resolver needs to exist that supports the referenced object (and needs to implement the 
  `ReferenceResolverInterface` interface)

## Roadmap

Development is currently frozen (this was the minimum viable product needed for another project), but the 
[TODO page](todo.md) holds a list of tasks and ideas that might get implemented if I get around to it.

## License

The library is licensed under the MIT license, of which the full text can be found in the [LICENSE](LICENSE) file.
