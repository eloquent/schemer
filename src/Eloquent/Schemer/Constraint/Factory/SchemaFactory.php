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
            $constraint = $this->createConstraint($property, $value, $schema);
            if ($constraint) {
                $constraints[] = $constraint;
            }
        }

        return new Schema($constraints);
    }

    /**
     * @param string         $property
     * @param ValueInterface $value
     * @param ObjectValue    $schema
     *
     * @return \Eloquent\Schemer\Constraint\ConstraintInterface|null
     */
    protected function createConstraint(
        $property,
        ValueInterface $value,
        ObjectValue $schema
    ) {
        switch ($property) {
            case 'type':
                return $this->createTypeConstraint($value);
        }
    }

    /**
     * @param ValueInterface $value
     *
     * @return TypeConstraint
     */
    protected function createTypeConstraint(ValueInterface $value)
    {
        return new TypeConstraint(ValueType::instanceByValue($value->value()));
    }
}
