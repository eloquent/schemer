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

use Eloquent\Schemer\Constraint\Generic\TypeConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertyConstraint;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\StringValue;
use Eloquent\Schemer\Value\ValueInterface;
use Eloquent\Schemer\Value\ValueType;

class SchemaFactory implements SchemaFactoryInterface
{
    /**
     * @param ObjectValue $schema
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function create(ObjectValue $schema)
    {
        $constraints = array();
        foreach ($schema as $property => $value) {
            $constraints = array_merge(
                $constraints,
                $this->createConstraints($property, $value, $schema)
            );
        }

        return new Schema($constraints);
    }

    /**
     * @param string         $property
     * @param ValueInterface $value
     * @param ObjectValue    $schema
     *
     * @return array<\Eloquent\Schemer\Constraint\ConstraintInterface>
     */
    protected function createConstraints(
        $property,
        ValueInterface $value,
        ObjectValue $schema
    ) {
        switch ($property) {
            // generic constraints
            case 'type':
                return array($this->createTypeConstraint($value));

            // object constraints
            case 'properties':
                return $this->createPropertyConstraints($value);
        }

        return array();
    }

    // generic constraints

    /**
     * @param ValueInterface $value
     *
     * @return TypeConstraint
     */
    protected function createTypeConstraint(ValueInterface $value)
    {
        if ($value instanceof StringValue) {
            return new TypeConstraint(
                array(ValueType::instanceByValue($value->value()))
            );
        } elseif ($value instanceof ArrayValue) {
            $types = array();
            foreach ($value as $type) {
                if (!$type instanceof StringValue) {
                    throw new UnexpectedValueException(
                        $type->type(),
                        array(ValueType::STRING_TYPE())
                    );
                }

                $types[] = ValueType::instanceByValue($type->value());
            }

            return new TypeConstraint($types);
        }

        throw new UnexpectedValueException(
            $value,
            array(ValueType::STRING_TYPE(), ValueType::ARRAY_TYPE())
        );
    }

    // object constraints

    /**
     * @param ValueInterface $value
     *
     * @return array<PropertyConstraint>
     */
    protected function createPropertyConstraints(ValueInterface $value)
    {
        if (!$value instanceof ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::OBJECT_TYPE())
            );
        }

        $constraints = array();
        foreach ($value as $property => $subValue) {
            $constraints[] = $this->createPropertyConstraint(
                $property,
                $subValue
            );
        }

        return $constraints;
    }

    /**
     * @param string         $property
     * @param ValueInterface $value
     *
     * @return PropertyConstraint
     */
    protected function createPropertyConstraint($property, ValueInterface $value)
    {
        if (!$value instanceof ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::OBJECT_TYPE())
            );
        }

        return new PropertyConstraint($property, $this->create($value));
    }
}
