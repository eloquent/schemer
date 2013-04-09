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
use Eloquent\Schemer\Value\ObjectValue;
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
            $subConstraints = $this->createConstraints($property, $value, $schema);
            if (count($subConstraints) > 0) {
                $constraints = array_merge($constraints, $subConstraints);
            }
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
    }

    // generic constraints

    /**
     * @param ValueInterface $value
     *
     * @return TypeConstraint
     */
    protected function createTypeConstraint(ValueInterface $value)
    {
        return new TypeConstraint(ValueType::instanceByValue($value->value()));
    }

    // object constraints

    /**
     * @param ObjectValue $value
     *
     * @return array<PropertyConstraint>
     */
    protected function createPropertyConstraints(ObjectValue $value)
    {
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
     * @param string      $property
     * @param ObjectValue $value
     *
     * @return PropertyConstraint
     */
    protected function createPropertyConstraint($property, ObjectValue $value)
    {
        return new PropertyConstraint($property, $this->create($value));
    }
}
