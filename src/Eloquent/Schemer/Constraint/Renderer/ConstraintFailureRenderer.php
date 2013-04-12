<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Renderer;

use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Constraint\Generic\AllOfConstraint;
use Eloquent\Schemer\Constraint\Generic\AnyOfConstraint;
use Eloquent\Schemer\Constraint\Generic\EnumConstraint;
use Eloquent\Schemer\Constraint\Generic\NotConstraint;
use Eloquent\Schemer\Constraint\Generic\OneOfConstraint;
use Eloquent\Schemer\Constraint\Generic\TypeConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\AdditionalPropertyConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MaximumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MinimumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\RequiredConstraint;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Value\ValueType;
use Icecave\Repr\Repr;

class ConstraintFailureRenderer implements ConstraintVisitorInterface
{
    const UNMATCHED_SCHEMA = 'The value did not match the defined schema.';

    /**
     * @param Schema $constraint
     *
     * @return string
     */
    public function visitSchema(Schema $constraint)
    {
        return static::UNMATCHED_SCHEMA;
    }

    // generic constraints =====================================================

    /**
     * @param EnumConstraint $constraint
     *
     * @return string
     */
    public function visitEnumConstraint(EnumConstraint $constraint)
    {
        $enumValues = array();
        foreach ($constraint->values() as $enumValue) {
            $enumValues[] = Repr::repr($enumValue->value());
        }

        if (count($enumValues) < 2) {
            return sprintf(
                'The value must be equal to %s.',
                array_pop($enumValues)
            );
        }

        return sprintf(
            'The value must be equal to one of the following: %s.',
            implode(', ', $enumValues)
        );
    }

    /**
     * @param TypeConstraint $constraint
     *
     * @return string
     */
    public function visitTypeConstraint(TypeConstraint $constraint)
    {
        $valueTypes = array_map(function (ValueType $valueType) {
            return Repr::repr($valueType->value());
        }, $constraint->valueTypes());

        if (count($valueTypes) < 2) {
            return sprintf(
                'The value must be of type %s.',
                array_pop($valueTypes)
            );
        }

        return sprintf(
            'The value must be one of the following types: %s.',
            implode(', ', $valueTypes)
        );
    }

    /**
     * @param AllOfConstraint $constraint
     *
     * @return string
     */
    public function visitAllOfConstraint(AllOfConstraint $constraint)
    {
        return 'The value did not match all of the defined schemas.';
    }

    /**
     * @param AnyOfConstraint $constraint
     *
     * @return string
     */
    public function visitAnyOfConstraint(AnyOfConstraint $constraint)
    {
        return 'The value did not match any of the defined schemas.';
    }

    /**
     * @param OneOfConstraint $constraint
     *
     * @return string
     */
    public function visitOneOfConstraint(OneOfConstraint $constraint)
    {
        return 'The value did not match any, or matched more than one of the defined schemas.';
    }

    /**
     * @param NotConstraint $constraint
     *
     * @return string
     */
    public function visitNotConstraint(NotConstraint $constraint)
    {
        return 'The value matched the defined schema.';
    }

    // object constraints ======================================================

    /**
     * @param MaximumPropertiesConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumPropertiesConstraint(MaximumPropertiesConstraint $constraint)
    {
        return sprintf(
            'The object must not have more than %s properties.',
            Repr::repr($constraint->maximum())
        );
    }

    /**
     * @param MinimumPropertiesConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumPropertiesConstraint(MinimumPropertiesConstraint $constraint)
    {
        return sprintf(
            'The object must not have less than %s properties.',
            Repr::repr($constraint->minimum())
        );
    }

    /**
     * @param RequiredConstraint $constraint
     *
     * @return string
     */
    public function visitRequiredConstraint(RequiredConstraint $constraint)
    {
        return sprintf(
            'The property %s is required.',
            Repr::repr($constraint->property())
        );
    }

    /**
     * @param PropertiesConstraint $constraint
     *
     * @return string
     */
    public function visitPropertiesConstraint(PropertiesConstraint $constraint)
    {
        return static::UNMATCHED_SCHEMA;
    }

    /**
     * @param AdditionalPropertyConstraint $constraint
     *
     * @return string
     */
    public function visitAdditionalPropertyConstraint(AdditionalPropertyConstraint $constraint)
    {
        return 'Unexpected property.';
    }
}
