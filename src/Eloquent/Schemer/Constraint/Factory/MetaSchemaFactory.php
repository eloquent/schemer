<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Factory;

use Eloquent\Schemer\Constraint\ArrayValue;
use Eloquent\Schemer\Constraint\Generic;
use Eloquent\Schemer\Constraint\NumberValue;
use Eloquent\Schemer\Constraint\ObjectValue;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\StringValue;
use Eloquent\Schemer\Value;

class MetaSchemaFactory
{
    /**
     * @return Schema
     */
    public function create()
    {
        $schema = new Schema(
            null,
            new Value\ObjectValue,
            'Schemer meta-schema',
            'Schema used to validate Schemer schemas.'
        );

        // definitions

        $schemaArraySchema = new Schema(
            array(
                new Generic\TypeConstraint(
                    array(Value\ValueType::ARRAY_TYPE())
                ),
                new ArrayValue\MinimumItemsConstraint(1),
                new ArrayValue\ItemsConstraint(array(), $schema),
            )
        );

        $positiveIntegerSchema = new Schema(
            array(
                new Generic\TypeConstraint(
                    array(Value\ValueType::INTEGER_TYPE())
                ),
                new NumberValue\MinimumConstraint(0),
            )
        );

        $positiveIntegerDefault0Schema = new Schema(
            array(
                new Generic\AllOfConstraint(
                    array(
                        $positiveIntegerSchema,
                        new Schema(null, new Value\IntegerValue(0)),
                    )
                ),
            )
        );

        $simpleTypesSchema = new Schema(
            array(
                new Generic\EnumConstraint(
                    new Value\ArrayValue(
                        array(
                            new Value\StringValue('array'),
                            new Value\StringValue('boolean'),
                            new Value\StringValue('date-time'),
                            new Value\StringValue('integer'),
                            new Value\StringValue('null'),
                            new Value\StringValue('number'),
                            new Value\StringValue('object'),
                            new Value\StringValue('string'),
                        )
                    )
                ),
            )
        );

        $stringArraySchema = new Schema(
            array(
                new Generic\TypeConstraint(
                    array(Value\ValueType::ARRAY_TYPE())
                ),
                new ArrayValue\ItemsConstraint(
                    array(),
                    new Schema(
                        array(
                            new Generic\TypeConstraint(
                                array(Value\ValueType::STRING_TYPE())
                            ),
                        )
                    )
                ),
            )
        );

        $dateOrDateStringSchema = new Schema(
            array(
                new Generic\AnyOfConstraint(
                    array(
                        new Schema(
                            array(
                                new Generic\TypeConstraint(
                                    array(Value\ValueType::DATE_TIME_TYPE())
                                ),
                            )
                        ),
                        new Schema(
                            array(
                                new Generic\TypeConstraint(
                                    array(Value\ValueType::STRING_TYPE())
                                ),
                                new StringValue\DateTimeFormatConstraint,
                            )
                        ),
                    )
                ),
            )
        );

        // constraints

        $typeConstraint = new Generic\TypeConstraint(
            array(Value\ValueType::OBJECT_TYPE())
        );

        $propertiesConstraint = new ObjectValue\PropertiesConstraint(
            array(
                'id' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::STRING_TYPE())
                        ),
                        new StringValue\UriFormatConstraint,
                    )
                ),

                '$schema' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::STRING_TYPE())
                        ),
                        new StringValue\UriFormatConstraint,
                    )
                ),

                'title' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::STRING_TYPE())
                        ),
                    )
                ),

                'description' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::STRING_TYPE())
                        ),
                    )
                ),

                'default' => new Schema,

                'multipleOf' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::NUMBER_TYPE())
                        ),
                        new NumberValue\MinimumConstraint(0, true),
                    )
                ),

                'maximum' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::NUMBER_TYPE())
                        ),
                    )
                ),

                'exclusiveMaximum' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::BOOLEAN_TYPE())
                        ),
                    ),
                    new Value\BooleanValue(false)
                ),

                'minimum' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::NUMBER_TYPE())
                        ),
                    )
                ),

                'exclusiveMinimum' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::BOOLEAN_TYPE())
                        ),
                    ),
                    new Value\BooleanValue(false)
                ),

                'maxLength' => $positiveIntegerSchema,
                'minLength' => $positiveIntegerDefault0Schema,

                'pattern' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::STRING_TYPE())
                        ),
                    )
                ),

                'additionalItems' => new Schema(
                    array(
                        new Generic\AnyOfConstraint(
                            array(
                                new Schema(
                                    array(
                                        new Generic\TypeConstraint(
                                            array(Value\ValueType::BOOLEAN_TYPE())
                                        ),
                                    )
                                ),
                                $schema,
                            )
                        ),
                    ),
                    new Value\ObjectValue
                ),

                'items' => new Schema(
                    array(
                        new Generic\AnyOfConstraint(
                            array($schema, $schemaArraySchema)
                        ),
                    ),
                    new Value\ObjectValue
                ),

                'maxItems' => $positiveIntegerSchema,
                'minItems' => $positiveIntegerDefault0Schema,

                'uniqueItems' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::BOOLEAN_TYPE())
                        ),
                    ),
                    new Value\BooleanValue(false)
                ),

                'maxProperties' => $positiveIntegerSchema,
                'minProperties' => $positiveIntegerDefault0Schema,
                'required' => $stringArraySchema,

                'additionalProperties' => new Schema(
                    array(
                        new Generic\AnyOfConstraint(
                            array(
                                new Schema(
                                    array(
                                        new Generic\TypeConstraint(
                                            array(Value\ValueType::BOOLEAN_TYPE())
                                        ),
                                    )
                                ),
                                $schema,
                            )
                        ),
                    ),
                    new Value\ObjectValue
                ),

                'definitions' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::OBJECT_TYPE())
                        ),
                        new ObjectValue\PropertiesConstraint(
                            array(),
                            array(),
                            $schema
                        ),
                    ),
                    new Value\ObjectValue
                ),

                'properties' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::OBJECT_TYPE())
                        ),
                        new ObjectValue\PropertiesConstraint(
                            array(),
                            array(),
                            $schema
                        ),
                    ),
                    new Value\ObjectValue
                ),

                'patternProperties' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::OBJECT_TYPE())
                        ),
                        new ObjectValue\PropertiesConstraint(
                            array(),
                            array(),
                            $schema
                        ),
                    ),
                    new Value\ObjectValue
                ),

                'dependencies' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::OBJECT_TYPE())
                        ),
                        new ObjectValue\PropertiesConstraint(
                            array(),
                            array(),
                            new Schema(
                                array(
                                    new Generic\AnyOfConstraint(
                                        array(
                                            $schema,
                                            $stringArraySchema,
                                        )
                                    ),
                                )
                            )
                        ),
                    )
                ),

                'enum' => new Schema(
                    array(
                        new Generic\TypeConstraint(
                            array(Value\ValueType::ARRAY_TYPE())
                        ),
                        new ArrayValue\MinimumItemsConstraint(1),
                        new ArrayValue\UniqueItemsConstraint(true),
                    )
                ),

                'type' => new Schema(
                    array(
                        new Generic\AnyOfConstraint(
                            array(
                                $simpleTypesSchema,
                                new Schema(
                                    array(
                                        new Generic\TypeConstraint(
                                            array(Value\ValueType::ARRAY_TYPE())
                                        ),
                                        new ArrayValue\ItemsConstraint(
                                            array(),
                                            $simpleTypesSchema
                                        ),
                                        new ArrayValue\MinimumItemsConstraint(1),
                                        new ArrayValue\UniqueItemsConstraint(true),
                                    )
                                ),
                            )
                        ),
                    )
                ),

                'allOf' => $schemaArraySchema,
                'anyOf' => $schemaArraySchema,
                'oneOf' => $schemaArraySchema,
                'not' => $schema,
                'maxDateTime' => $dateOrDateStringSchema,
                'minDateTime' => $dateOrDateStringSchema,
            ),
            array(),
            new Schema
        );

        $exclusiveMaximumDependencyConstraint = new ObjectValue\DependencyConstraint(
            'exclusiveMaximum',
            new Schema(
                array(
                    new ObjectValue\RequiredConstraint('maximum')
                )
            )
        );

        $exclusiveMinimumDependencyConstraint = new ObjectValue\DependencyConstraint(
            'exclusiveMinimum',
            new Schema(
                array(
                    new ObjectValue\RequiredConstraint('minimum')
                )
            )
        );

        // composition

        $schema->setConstraints(
            array(
                $typeConstraint,
                $propertiesConstraint,
                $exclusiveMaximumDependencyConstraint,
                $exclusiveMinimumDependencyConstraint,
            )
        );

        return $schema;
    }
}
