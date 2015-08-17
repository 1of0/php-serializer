# API

## Classes

### JsonConvert

The `JsonConvert` class provides a static interface to a `Serializer` instance with default configuration. The method
`JsonConvert::toJson()` performs serialization and `JsonConvert::fromJson()` performs deserialization.

### Serializer

The `Serializer` class is a non-static class that provides configurable functionality. The configuration is provided
through a `Configuration` instance as a constructor argument. Methods `serialize()` and `deserialize()` will provide
serialization and deserialization.

### Configuration

The `Configuration` class allows controlling behaviour such as *pretty printing*, including or excluding null values
during serialization, and maximum depth for serialization and deserialization.

## Interfaces

### JsonConverterInterface

Implementing the `JsonConverterInterface` allows customization to the serialization process on a per-property basis. By
annotating properties/methods that need custom serialization with a `@CustomConverter` annotation, and providing it
with the implementation's class name, the serializer will use the implementation to serialize the property.

### ReferableInterface

The `ReferableInterface` should be implemented by any class that may represent a reference. This way properties may
specifically declare whether a sub-object will be fully serialized, or whether only its reference will be serialized.
Note, a referable class must also declare a `@Repository` annotation with a repository class as value to indicate a
source that returns full instances from only a reference.

### RepositoryInterface

The `RepositoryInterface` should be implemented for any `ReferableInterface` implementation, as it is needed to resolve
object instances from references. An implementation may for example connect to an ORM, or may provide a lazy instance.

## Built-in converters

### DateTimeConverter

The `DateTimeConverter` class is an implementation of the `JsonConverterInterface`, that converts DateTime objects into
unix timestamps. It may be applied on properties that are `DateTime` objects or `DateTime` derivatives (such as
`Carbon`).

## Annotations

### @NoMetadata

### @ExplicitInclusion

### @Ignore

### @Property

### @Getter

### @Setter

### @Type

### @IsReference

### @IsArray

### @CustomConverter
