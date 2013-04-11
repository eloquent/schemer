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

use Eloquent\Schemer\Constraint\Generic\AllOfConstraint;
use Eloquent\Schemer\Constraint\Generic\AnyOfConstraint;
use Eloquent\Schemer\Constraint\Generic\NotConstraint;
use Eloquent\Schemer\Constraint\Generic\OneOfConstraint;
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
            case 'allOf':
                return array($this->createAllOfConstraint($value));
            case 'anyOf':
                return array($this->createAnyOfConstraint($value));
            case 'oneOf':
                return array($this->createOneOfConstraint($value));
            case 'not':
                return array($this->createNotConstraint($value));

            // object constraints
            case 'properties':
                return $this->createPropertyConstraints($value);
        }

        return array();
    }

    // generic constraints =====================================================

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
            $valueTypes = array();
            foreach ($value as $typeValue) {
                if (!$typeValue instanceof StringValue) {
                    throw new UnexpectedValueException(
                        $typeValue->valueType(),
                        array(ValueType::STRING_TYPE())
                    );
                }

                $valueTypes[] = ValueType::instanceByValue($typeValue->value());
            }

            return new TypeConstraint($valueTypes);
        }

        throw new UnexpectedValueException(
            $value,
            array(ValueType::STRING_TYPE(), ValueType::ARRAY_TYPE())
        );
    }

    /**
     * @param ValueInterface $value
     *
     * @return AllOfConstraint
     */
    protected function createAllOfConstraint(ValueInterface $value)
    {
        if (!$value instanceof ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::ARRAY_TYPE())
            );
        }

        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->create($subValue);
        }

        return new AllOfConstraint($schemas);
    }

    /**
     * @param ValueInterface $value
     *
     * @return AnyOfConstraint
     */
    protected function createAnyOfConstraint(ValueInterface $value)
    {
        if (!$value instanceof ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::ARRAY_TYPE())
            );
        }

        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->create($subValue);
        }

        return new AnyOfConstraint($schemas);
    }

    /**
     * @param ValueInterface $value
     *
     * @return OneOfConstraint
     */
    protected function createOneOfConstraint(ValueInterface $value)
    {
        if (!$value instanceof ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::ARRAY_TYPE())
            );
        }

        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->create($subValue);
        }

        return new OneOfConstraint($schemas);
    }

    /**
     * @param ValueInterface $value
     *
     * @return NotConstraint
     */
    protected function createNotConstraint(ValueInterface $value)
    {
        return new NotConstraint($this->create($value));
    }

    // object constraints ======================================================

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
