<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Constraint;

use DateTime;
use Eloquent\Liberator\Liberator;
use Eloquent\Schemer\Constraint\ArrayValue\ItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MaximumItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MinimumItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\UniqueItemsConstraint;
use Eloquent\Schemer\Constraint\DateTimeValue\MaximumDateTimeConstraint;
use Eloquent\Schemer\Constraint\DateTimeValue\MinimumDateTimeConstraint;
use Eloquent\Schemer\Constraint\Generic\AllOfConstraint;
use Eloquent\Schemer\Constraint\Generic\AnyOfConstraint;
use Eloquent\Schemer\Constraint\Generic\EnumerationConstraint;
use Eloquent\Schemer\Constraint\Generic\NotConstraint;
use Eloquent\Schemer\Constraint\Generic\OneOfConstraint;
use Eloquent\Schemer\Constraint\Generic\TypeConstraint;
use Eloquent\Schemer\Constraint\NumberValue\MaximumConstraint;
use Eloquent\Schemer\Constraint\NumberValue\MinimumConstraint;
use Eloquent\Schemer\Constraint\NumberValue\MultipleOfConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\DependenciesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MaximumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MinimumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\RequiredConstraint;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\StringValue\Format\DateTimeFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\EmailFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\HostnameFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\Ipv4AddressFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\Ipv6AddressFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\UriFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\MaximumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\MinimumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\PatternConstraint;
use PHPUnit_Framework_TestCase;
use stdClass;

class ObjectMapSchemaSpecificationGeneratorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->generator = new ObjectMapSchemaSpecificationGenerator;

        $this->schema = new Schema;
    }

    public function testEmptySchema()
    {
        $schema = new Schema;

        $this->assertEquals(new stdClass, $this->generator->schemaToSpecification($schema));
    }

    public function testSimpleSchema()
    {
        $this->schema->setTitle('root-schema');
        $this->schema->setDescription('description');
        $this->schema->setDefaultValue('default-value');
        $this->schema->addConstraint(new MinimumConstraint(111));
        $this->schema->addConstraint(new MaximumConstraint(222));
        $this->schema->addConstraint(
            new ItemsConstraint(
                [
                    new Schema(
                        [
                            new MinimumConstraint(333),
                            new MaximumConstraint(444),
                        ]
                    ),
                ]
            )
        );
        $expected = (object) [
            'title' => 'root-schema',
            'description' => 'description',
            'defaultValue' => 'default-value',
            'minimum' => 111,
            'maximum' => 222,
            'items' => [
                (object) [
                    'minimum' => 333,
                    'maximum' => 444,
                ],
            ]
        ];

        $this->assertEquals($expected, $this->generator->schemaToSpecification($this->schema));
    }

    public function testRecursiveSchema()
    {
        $this->schema->addConstraint(new ItemsConstraint(null, $this->schema));
        $expected = new stdClass;
        $expected->additionalItems = $expected;

        $this->assertEquals($expected, $this->generator->schemaToSpecification($this->schema));
    }

    public function testConstraints()
    {
        $this->schema->addConstraint(
            new ItemsConstraint(
                [
                    new Schema([], null, 'item-0-schema'),
                    new Schema([], null, 'item-1-schema'),
                ],
                new Schema([], null, 'additional-item-schema')
            )
        );
        $this->schema->addConstraint(new MaximumItemsConstraint(111));
        $this->schema->addConstraint(new MinimumItemsConstraint(222));
        $this->schema->addConstraint(new UniqueItemsConstraint);
        $this->schema->addConstraint(new MaximumDateTimeConstraint(new DateTime('2015-10-21T16:29:00Z')));
        $this->schema->addConstraint(new MinimumDateTimeConstraint(new DateTime('1885-09-02T12:00:00Z')));
        $this->schema->addConstraint(
            new AllOfConstraint(
                [
                    new Schema([], null, 'all-of-0-schema'),
                    new Schema([], null, 'all-of-1-schema'),
                ]
            )
        );
        $this->schema->addConstraint(
            new AnyOfConstraint(
                [
                    new Schema([], null, 'any-of-0-schema'),
                    new Schema([], null, 'any-of-1-schema'),
                ]
            )
        );
        $this->schema->addConstraint(new EnumerationConstraint(['enum-string', 333]));
        $this->schema->addConstraint(new NotConstraint(new Schema([], null, 'not-schema')));
        $this->schema->addConstraint(
            new OneOfConstraint(
                [
                    new Schema([], null, 'one-of-0-schema'),
                    new Schema([], null, 'one-of-1-schema'),
                ]
            )
        );
        $this->schema->addConstraint(new TypeConstraint(['string', 'boolean']));
        $this->schema->addConstraint(new MinimumConstraint(444));
        $this->schema->addConstraint(new MaximumConstraint(555));
        $this->schema->addConstraint(new MultipleOfConstraint(666));
        $this->schema->addConstraint(
            new DependenciesConstraint(
                [
                    'dependency-property-a' => new Schema([], null, 'dependency-a-schema'),
                    'dependency-property-b' => ['dependency-property-c', 'dependency-property-d'],
                ]
            )
        );
        $this->schema->addConstraint(new MaximumPropertiesConstraint(777));
        $this->schema->addConstraint(new MinimumPropertiesConstraint(888));
        $this->schema->addConstraint(
            new PropertiesConstraint(
                [
                    'property-a' => new Schema([], null, 'property-a-schema'),
                    'property-b' => new Schema([], null, 'property-b-schema'),
                ],
                [
                    'pattern-a' => new Schema([], null, 'pattern-a-schema'),
                    'pattern-b' => new Schema([], null, 'pattern-b-schema'),
                ],
                new Schema([], null, 'additional-property-schema')
            )
        );
        $this->schema->addConstraint(new RequiredConstraint(['required-property-a', 'required-property-b']));
        $this->schema->addConstraint(new MaximumLengthConstraint(999));
        $this->schema->addConstraint(new MinimumLengthConstraint(1111));
        $this->schema->addConstraint(new PatternConstraint('pattern'));
        $expected = (object) [
            'items' => [
                (object) ['title' => 'item-0-schema'],
                (object) ['title' => 'item-1-schema'],
            ],
            'additionalItems' => (object) ['title' => 'additional-item-schema'],
            'maxItems' => 111,
            'minItems' => 222,
            'uniqueItems' => true,
            'maxDateTime' => new DateTime('2015-10-21T16:29:00Z'),
            'minDateTime' => new DateTime('1885-09-02T12:00:00Z'),
            'allOf' => [
                (object) ['title' => 'all-of-0-schema'],
                (object) ['title' => 'all-of-1-schema'],
            ],
            'anyOf' => [
                (object) ['title' => 'any-of-0-schema'],
                (object) ['title' => 'any-of-1-schema'],
            ],
            'enum' => ['enum-string', 333],
            'not' => (object) ['title' => 'not-schema'],
            'oneOf' => [
                (object) ['title' => 'one-of-0-schema'],
                (object) ['title' => 'one-of-1-schema'],
            ],
            'type' => ['string', 'boolean'],
            'minimum' => 444,
            'maximum' => 555,
            'multipleOf' => 666,
            'dependencies' => (object) [
                'dependency-property-a' => (object) ['title' => 'dependency-a-schema'],
                'dependency-property-b' => ['dependency-property-c', 'dependency-property-d'],
            ],
            'maxProperties' => 777,
            'minProperties' => 888,
            'properties' => (object) [
                'property-a' => (object) ['title' => 'property-a-schema'],
                'property-b' => (object) ['title' => 'property-b-schema'],
            ],
            'patternProperties' => (object) [
                'pattern-a' => (object) ['title' => 'pattern-a-schema'],
                'pattern-b' => (object) ['title' => 'pattern-b-schema'],
            ],
            'additionalProperties' => (object) ['title' => 'additional-property-schema'],
            'required' => ['required-property-a', 'required-property-b'],
            'maxLength' => 999,
            'minLength' => 1111,
            'pattern' => 'pattern',
        ];

        $this->assertEquals($expected, $this->generator->schemaToSpecification($this->schema));
    }

    public function formatConstraintData()
    {
        //                  constraint                       format
        return [
            'Date-time' => [new DateTimeFormatConstraint,    'date-time'],
            'Email'     => [new EmailFormatConstraint,       'email'],
            'Hostname'  => [new HostnameFormatConstraint,    'hostname'],
            'IPv4'      => [new Ipv4AddressFormatConstraint, 'ipv4'],
            'IPv6'      => [new Ipv6AddressFormatConstraint, 'ipv6'],
            'URI'       => [new UriFormatConstraint,         'uri'],
        ];
    }

    /**
     * @dataProvider formatConstraintData
     */
    public function testFormatConstraints($constraint, $format)
    {
        $this->schema->addConstraint($constraint);
        $expected = (object) ['format' => $format];

        $this->assertEquals($expected, $this->generator->schemaToSpecification($this->schema));
    }

    public function testInstance()
    {
        $class = get_class($this->generator);
        Liberator::liberateClass($class)->instance = null;
        $actual = $class::instance();

        $this->assertInstanceOf($class, $actual);
        $this->assertSame($actual, $class::instance());
    }
}
