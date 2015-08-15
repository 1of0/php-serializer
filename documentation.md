# Annotations

## @InclusionStrategy

```
@InclusionStrategy(int value)
```

##### Description

By default the serializer will serialize from and deserialize to all public properties defined in a class. To change
this behaviour, the `@InclusionStrategy` annotation can be defined on a class to specify whether the inclusion
behaviour should be **implicit** or **explicit**.

If the strategy is set to **explicit**, only members marked with a `@JsonProperty`, `@JsonGetter`, or `@JsonSetter`
attribute will be serialized.

When the strategy is **implicit**, all public properties are serialized unless they are marked with a `@JsonIgnore`
attribute. Getter or setter methods always need to be explicitly marked with a `@JsonGetter` or `@JsonSetter` attribute.

##### Example usage

```php
/**
 * @InclusionStrategy(InclusionStrategy::EXPLICIT)
 */
class Foo
```

## @JsonConverter

```
@JsonConverter(string value[, bool serialize = true, bool deserialize = true])
```

##### Description

The `@JsonConverter` attribute allows the configuration of a custom conversion during serialization or deserialization.
The `class` parameter of the attribute should be the fully qualified class name of a converter class that implements the
`JsonConverterInterface` interface.

Additionally the boolean properties `serialize` and `deserialize` allow to specify whether the converter should be
applied during serialization, deserialization, or both.

##### Example usage

```php
/**
 * @JsonConverter(\OneOfZero\Json\Converters\DateTimeConverter::class)
 */
public $timestamp;
```

## @JsonProperty

```
@JsonProperty([string value, string class, bool isArray = false, bool isReference = false, bool serialize = true, bool deserialize = true])
```

##### Description

##### Example usage

```php
/**
 *
 */
```

## @JsonGetter

```
@JsonGetter([string propertyName, string class, bool isArray = false, bool isReference = false])
```

##### Description

##### Example usage

```php
/**
 *
 */
```

## @JsonSetter

```
@JsonSetter([string propertyName, string class, bool isArray = false, bool isReference = false])
```

##### Description

##### Example usage

```php
/**
 *
 */
```

## @JsonIgnore

```
@JsonIgnore([ignoreOnSerialize=true, ignoreOnSerialize=true])
```

##### Description

##### Example usage

```php
/**
 *
 */
```

## @Repository

```
@Repository(string value)
```

##### Description

##### Example usage

```php
/**
 *
 */
```

## @NoMetaData

```
@NoMetaData
```

##### Description

##### Example usage

```php
/**
 *
 */
```
