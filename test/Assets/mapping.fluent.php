<?php

use OneOfZero\Json\Fluent\Mapping;
use OneOfZero\Json\Converters\DateTimeConverter;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingClassLevelConverter;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingConverters;
use OneOfZero\Json\Test\FixtureClasses\ClassUsingDifferentClassLevelConverters;
use OneOfZero\Json\Test\FixtureClasses\ClassWithGetterAndSetter;
use OneOfZero\Json\Test\FixtureClasses\ClassWithGetterAndSetterOnProperty;
use OneOfZero\Json\Test\FixtureClasses\ClassWithInvalidGetterAndSetter;
use OneOfZero\Json\Test\FixtureClasses\Converters\ClassDependentMemberConverter;
use OneOfZero\Json\Test\FixtureClasses\Converters\ContextSensitiveMemberConverter;
use OneOfZero\Json\Test\FixtureClasses\Converters\DeserializingMemberConverter;
use OneOfZero\Json\Test\FixtureClasses\Converters\DeserializingObjectConverter;
use OneOfZero\Json\Test\FixtureClasses\Converters\PropertyDependentMemberConverter;
use OneOfZero\Json\Test\FixtureClasses\Converters\SerializingMemberConverter;
use OneOfZero\Json\Test\FixtureClasses\Converters\SerializingObjectConverter;
use OneOfZero\Json\Test\FixtureClasses\Converters\SimpleObjectConverter;
use OneOfZero\Json\Test\FixtureClasses\ReferableClass;
use OneOfZero\Json\Test\FixtureClasses\SimpleClass;

$mapping = new Mapping();

return $mapping
	->forClass(SimpleClass::class)
		->property('foo')->name('food')->done()
		->property('bar')->setIncluded()->done()
		->property('baz')->setIgnored()->done()
		->done()

	->forClass(ClassUsingConverters::class)
		->property('dateObject')
			->type(DateTime::class)
			->converter(DateTimeConverter::class)
			->done()
		->property('simpleClass')
			->type(SimpleClass::class)
			->converter(ClassDependentMemberConverter::class)
			->done()
		->property('referableClass')
			->type(ReferableClass::class)
			->converter(ClassDependentMemberConverter::class)
			->done()
		->property('differentConverters')
			->serializingConverter(SerializingMemberConverter::class)
			->deserializingConverter(DeserializingMemberConverter::class)
			->done()
		->property('foo')->converter(PropertyDependentMemberConverter::class)->done()
		->property('bar')->converter(PropertyDependentMemberConverter::class)->done()
		->property('contextSensitive')->converter(ContextSensitiveMemberConverter::class)->done()
		->getter('getPrivateDateObject')
			->type(DateTime::class)
			->converter(DateTimeConverter::class)
			->done()
		->setter('setPrivateDateObject')
			->type(DateTime::class)
			->converter(DateTimeConverter::class)
			->done()
		->done()

	->forClass(ClassUsingClassLevelConverter::class)
		->converter(SimpleObjectConverter::class)
		->done()

	->forClass(ClassUsingDifferentClassLevelConverters::class)
		->serializingConverter(SerializingObjectConverter::class)
		->deserializingConverter(DeserializingObjectConverter::class)
		->done()

	->forClass(ClassWithGetterAndSetter::class)
		->getter('getFoo')->done()
		->setter('setFoo')->done()
		->done()

	->forClass(ClassWithInvalidGetterAndSetter::class)
		->getter('getFoo')->done()
		->setter('setFoo')->done()
		->done()

	->forClass(ClassWithGetterAndSetterOnProperty::class)
		->getter('foo')->done()
		->setter('bar')->done()
		->done()
;
