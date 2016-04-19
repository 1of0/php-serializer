<?php

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

return
[
	SimpleClass::class => [
		'properties' => [
			'foo' => [ 'name' => 'food' ],
			'bar' => [ 'include' => true ],
			'baz' => [ 'ignore' => true ],
		],
	],

	ClassUsingConverters::class => [
		'properties' => [
			'dateObject' => [
				'type' => DateTime::class,
				'converter' => DateTimeConverter::class,
			],
			'simpleClass' => [
				'type' => SimpleClass::class,
				'converter' => ClassDependentMemberConverter::class,
			],
			'referableClass' => [
				'type' => ReferableClass::class,
				'converter' => ClassDependentMemberConverter::class,
			],
			'differentConverters' => [
				'converters' => [
					'serializer' => SerializingMemberConverter::class,
					'deserializer' => DeserializingMemberConverter::class,
				],
			],
			'foo' => [
				'converter' => PropertyDependentMemberConverter::class,
			],
			'bar' => [
				'converter' => PropertyDependentMemberConverter::class,
			],
			'contextSensitive' => [
				'converter' => ContextSensitiveMemberConverter::class,
			],
		],
		'methods' => [
			'getPrivateDateObject' => [
				'getter' => true,
				'type' => DateTime::class,
				'converter' => DateTimeConverter::class,
			],
			'setPrivateDateObject' => [
				'setter' => true,
				'type' => DateTime::class,
				'converter' => DateTimeConverter::class,
			],
		],
	],

	ClassUsingClassLevelConverter::class => [
		'converter' => SimpleObjectConverter::class,
	],

	ClassUsingDifferentClassLevelConverters::class => [
		'converters' => [
			'serializer' => SerializingObjectConverter::class,
			'deserializer' => DeserializingObjectConverter::class,
		],
	],

	ClassWithGetterAndSetter::class => [
		'methods' => [
			'getFoo' => [ 'getter' => true ],
			'setFoo' => [ 'setter' => true ],
		],
	],

	ClassWithInvalidGetterAndSetter::class => [
		'methods' => [
			'getFoo' => [ 'getter' => true ],
			'setFoo' => [ 'setter' => true ],
		],
	],

	ClassWithGetterAndSetterOnProperty::class => [
		'properties' => [
			'foo' => [ 'getter' => true ],
			'bar' => [ 'setter' => true ],
		],
	],
];