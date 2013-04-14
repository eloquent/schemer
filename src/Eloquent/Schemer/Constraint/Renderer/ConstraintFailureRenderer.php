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

use Eloquent\Schemer\Constraint\ArrayValue\AdditionalItemConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\ItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MaximumItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MinimumItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\UniqueItemsConstraint;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Constraint\DateTimeValue\MaximumDateTimeConstraint;
use Eloquent\Schemer\Constraint\DateTimeValue\MinimumDateTimeConstraint;
use Eloquent\Schemer\Constraint\Generic\AllOfConstraint;
use Eloquent\Schemer\Constraint\Generic\AnyOfConstraint;
use Eloquent\Schemer\Constraint\Generic\EnumConstraint;
use Eloquent\Schemer\Constraint\Generic\NotConstraint;
use Eloquent\Schemer\Constraint\Generic\OneOfConstraint;
use Eloquent\Schemer\Constraint\Generic\TypeConstraint;
use Eloquent\Schemer\Constraint\NumberValue\MaximumConstraint;
use Eloquent\Schemer\Constraint\NumberValue\MinimumConstraint;
use Eloquent\Schemer\Constraint\NumberValue\MultipleOfConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\AdditionalPropertyConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\DependencyConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MaximumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MinimumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\RequiredConstraint;
use Eloquent\Schemer\Constraint\StringValue\DateTimeFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\EmailFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\MaximumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\MinimumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\PatternConstraint;
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

    /**
     * @param DependencyConstraint $constraint
     *
     * @return string
     */
    public function visitDependencyConstraint(DependencyConstraint $constraint)
    {
        return static::UNMATCHED_SCHEMA;
    }

    // array constraints =======================================================

    /**
     * @param ItemsConstraint $constraint
     *
     * @return string
     */
    public function visitItemsConstraint(ItemsConstraint $constraint)
    {
        return static::UNMATCHED_SCHEMA;
    }

    /**
     * @param AdditionalItemConstraint $constraint
     *
     * @return string
     */
    public function visitAdditionalItemConstraint(AdditionalItemConstraint $constraint)
    {
        return 'Unexpected index.';
    }

    /**
     * @param MaximumItemsConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumItemsConstraint(MaximumItemsConstraint $constraint)
    {
        return sprintf(
            'The array must not have more than %s items.',
            Repr::repr($constraint->maximum())
        );
    }

    /**
     * @param MinimumItemsConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumItemsConstraint(MinimumItemsConstraint $constraint)
    {
        return sprintf(
            'The array must not have less than %s items.',
            Repr::repr($constraint->minimum())
        );
    }

    /**
     * @param UniqueItemsConstraint $constraint
     *
     * @return string
     */
    public function visitUniqueItemsConstraint(UniqueItemsConstraint $constraint)
    {
        return 'The array items must be unique.';
    }

    // string constraints ======================================================

    /**
     * @param MaximumLengthConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumLengthConstraint(MaximumLengthConstraint $constraint)
    {
        return sprintf(
            'The string must not have more than %s characters.',
            Repr::repr($constraint->maximum())
        );
    }

    /**
     * @param MinimumLengthConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumLengthConstraint(MinimumLengthConstraint $constraint)
    {
        return sprintf(
            'The string must not have less than %s characters.',
            Repr::repr($constraint->minimum())
        );
    }

    /**
     * @param PatternConstraint $constraint
     *
     * @return string
     */
    public function visitPatternConstraint(PatternConstraint $constraint)
    {
        return sprintf(
            'The string must match the pattern %s.',
            Repr::repr($constraint->pattern())
        );
    }

    /**
     * @param DateTimeFormatConstraint $constraint
     *
     * @return string
     */
    public function visitDateTimeFormatConstraint(DateTimeFormatConstraint $constraint)
    {
        return 'The string must be a valid ISO 8601 date/time.';
    }

    /**
     * @param EmailFormatConstraint $constraint
     *
     * @return string
     */
    public function visitEmailFormatConstraint(EmailFormatConstraint $constraint)
    {
        return 'The string must be a valid email address.';
    }

    // number constraints ======================================================

    /**
     * @param MultipleOfConstraint $constraint
     *
     * @return string
     */
    public function visitMultipleOfConstraint(MultipleOfConstraint $constraint)
    {
        return sprintf(
            'The number must be a multiple of %s.',
            Repr::repr($constraint->quantity())
        );
    }

    /**
     * @param MaximumConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumConstraint(MaximumConstraint $constraint)
    {
        return sprintf(
            'The number must not be more than %s.',
            Repr::repr($constraint->maximum())
        );
    }

    /**
     * @param MinimumConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumConstraint(MinimumConstraint $constraint)
    {
        return sprintf(
            'The number must not be less than %s.',
            Repr::repr($constraint->minimum())
        );
    }

    // date-time constraints ===================================================

    /**
     * @param MaximumDateTimeConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumDateTimeConstraint(MaximumDateTimeConstraint $constraint)
    {
        return sprintf(
            'The date-time value must not be after %s.',
            Repr::repr($constraint->maximum()->format('c'))
        );
    }

    /**
     * @param MinimumDateTimeConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumDateTimeConstraint(MinimumDateTimeConstraint $constraint)
    {
        return sprintf(
            'The date-time value must not be before %s.',
            Repr::repr($constraint->minimum()->format('c'))
        );
    }
}
