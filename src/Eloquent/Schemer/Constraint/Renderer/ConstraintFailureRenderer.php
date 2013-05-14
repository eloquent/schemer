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

use Eloquent\Schemer\Constraint\ArrayValue;
use Eloquent\Schemer\Constraint\DateTimeValue;
use Eloquent\Schemer\Constraint\Generic;
use Eloquent\Schemer\Constraint\NumberValue;
use Eloquent\Schemer\Constraint\ObjectValue;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\StringValue;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;
use Eloquent\Schemer\Value\ValueType;

class ConstraintFailureRenderer implements ConstraintVisitorInterface
{
    const UNMATCHED_SCHEMA = 'The value did not match the defined schema.';

    /**
     * @param Schema $schema
     *
     * @return string
     */
    public function visitSchema(Schema $schema)
    {
        return static::UNMATCHED_SCHEMA;
    }

    // generic constraints =====================================================

    /**
     * @param Generic\EnumConstraint $constraint
     *
     * @return string
     */
    public function visitEnumConstraint(Generic\EnumConstraint $constraint)
    {
        $enumValues = array();
        foreach ($constraint->values() as $enumValue) {
            $enumValues[] = var_export($enumValue->value(), true);
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
     * @param Generic\TypeConstraint $constraint
     *
     * @return string
     */
    public function visitTypeConstraint(Generic\TypeConstraint $constraint)
    {
        $valueTypes = array_map(function (ValueType $valueType) {
            return var_export($valueType->value(), true);
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
     * @param Generic\AllOfConstraint $constraint
     *
     * @return string
     */
    public function visitAllOfConstraint(Generic\AllOfConstraint $constraint)
    {
        return 'The value did not match all of the defined schemas.';
    }

    /**
     * @param Generic\AnyOfConstraint $constraint
     *
     * @return string
     */
    public function visitAnyOfConstraint(Generic\AnyOfConstraint $constraint)
    {
        return 'The value did not match any of the defined schemas.';
    }

    /**
     * @param Generic\OneOfConstraint $constraint
     *
     * @return string
     */
    public function visitOneOfConstraint(Generic\OneOfConstraint $constraint)
    {
        return 'The value did not match any, or matched more than one of the defined schemas.';
    }

    /**
     * @param Generic\NotConstraint $constraint
     *
     * @return string
     */
    public function visitNotConstraint(Generic\NotConstraint $constraint)
    {
        return 'The value matched the defined schema.';
    }

    // object constraints ======================================================

    /**
     * @param ObjectValue\MaximumPropertiesConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumPropertiesConstraint(ObjectValue\MaximumPropertiesConstraint $constraint)
    {
        return sprintf(
            'The object must not have more than %s properties.',
            var_export($constraint->maximum(), true)
        );
    }

    /**
     * @param ObjectValue\MinimumPropertiesConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumPropertiesConstraint(ObjectValue\MinimumPropertiesConstraint $constraint)
    {
        return sprintf(
            'The object must not have less than %s properties.',
            var_export($constraint->minimum(), true)
        );
    }

    /**
     * @param ObjectValue\RequiredConstraint $constraint
     *
     * @return string
     */
    public function visitRequiredConstraint(ObjectValue\RequiredConstraint $constraint)
    {
        return sprintf(
            'The property %s is required.',
            var_export($constraint->property(), true)
        );
    }

    /**
     * @param ObjectValue\PropertiesConstraint $constraint
     *
     * @return string
     */
    public function visitPropertiesConstraint(ObjectValue\PropertiesConstraint $constraint)
    {
        return static::UNMATCHED_SCHEMA;
    }

    /**
     * @param ObjectValue\AdditionalPropertyConstraint $constraint
     *
     * @return string
     */
    public function visitAdditionalPropertyConstraint(ObjectValue\AdditionalPropertyConstraint $constraint)
    {
        return 'Unexpected property.';
    }

    /**
     * @param ObjectValue\DependencyConstraint $constraint
     *
     * @return string
     */
    public function visitDependencyConstraint(ObjectValue\DependencyConstraint $constraint)
    {
        return static::UNMATCHED_SCHEMA;
    }

    // array constraints =======================================================

    /**
     * @param ArrayValue\ItemsConstraint $constraint
     *
     * @return string
     */
    public function visitItemsConstraint(ArrayValue\ItemsConstraint $constraint)
    {
        return static::UNMATCHED_SCHEMA;
    }

    /**
     * @param ArrayValue\AdditionalItemConstraint $constraint
     *
     * @return string
     */
    public function visitAdditionalItemConstraint(ArrayValue\AdditionalItemConstraint $constraint)
    {
        return 'Unexpected index.';
    }

    /**
     * @param ArrayValue\MaximumItemsConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumItemsConstraint(ArrayValue\MaximumItemsConstraint $constraint)
    {
        return sprintf(
            'The array must not have more than %s items.',
            var_export($constraint->maximum(), true)
        );
    }

    /**
     * @param ArrayValue\MinimumItemsConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumItemsConstraint(ArrayValue\MinimumItemsConstraint $constraint)
    {
        return sprintf(
            'The array must not have less than %s items.',
            var_export($constraint->minimum(), true)
        );
    }

    /**
     * @param ArrayValue\UniqueItemsConstraint $constraint
     *
     * @return string
     */
    public function visitUniqueItemsConstraint(ArrayValue\UniqueItemsConstraint $constraint)
    {
        return 'The array items must be unique.';
    }

    // string constraints ======================================================

    /**
     * @param StringValue\MaximumLengthConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumLengthConstraint(StringValue\MaximumLengthConstraint $constraint)
    {
        return sprintf(
            'The string must not have more than %s characters.',
            var_export($constraint->maximum(), true)
        );
    }

    /**
     * @param StringValue\MinimumLengthConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumLengthConstraint(StringValue\MinimumLengthConstraint $constraint)
    {
        return sprintf(
            'The string must not have less than %s characters.',
            var_export($constraint->minimum(), true)
        );
    }

    /**
     * @param StringValue\PatternConstraint $constraint
     *
     * @return string
     */
    public function visitPatternConstraint(StringValue\PatternConstraint $constraint)
    {
        return sprintf(
            'The string must match the pattern %s.',
            var_export($constraint->pattern(), true)
        );
    }

    /**
     * @param StringValue\DateTimeFormatConstraint $constraint
     *
     * @return string
     */
    public function visitDateTimeFormatConstraint(StringValue\DateTimeFormatConstraint $constraint)
    {
        return 'The string must be a valid ISO 8601 date/time.';
    }

    /**
     * @param StringValue\EmailFormatConstraint $constraint
     *
     * @return string
     */
    public function visitEmailFormatConstraint(StringValue\EmailFormatConstraint $constraint)
    {
        return 'The string must be a valid email address.';
    }

    /**
     * @param StringValue\HostnameFormatConstraint $constraint
     *
     * @return string
     */
    public function visitHostnameFormatConstraint(StringValue\HostnameFormatConstraint $constraint)
    {
        return 'The string must be a valid hostname.';
    }

    /**
     * @param StringValue\Ipv4AddressFormatConstraint $constraint
     *
     * @return string
     */
    public function visitIpv4AddressFormatConstraint(StringValue\Ipv4AddressFormatConstraint $constraint)
    {
        return 'The string must be a valid IPv4 address.';
    }

    /**
     * @param StringValue\Ipv6AddressFormatConstraint $constraint
     *
     * @return string
     */
    public function visitIpv6AddressFormatConstraint(StringValue\Ipv6AddressFormatConstraint $constraint)
    {
        return 'The string must be a valid IPv6 address.';
    }

    /**
     * @param StringValue\UriFormatConstraint $constraint
     *
     * @return string
     */
    public function visitUriFormatConstraint(StringValue\UriFormatConstraint $constraint)
    {
        return 'The string must be a valid URI.';
    }

    // number constraints ======================================================

    /**
     * @param NumberValue\MultipleOfConstraint $constraint
     *
     * @return string
     */
    public function visitMultipleOfConstraint(NumberValue\MultipleOfConstraint $constraint)
    {
        return sprintf(
            'The number must be a multiple of %s.',
            var_export($constraint->quantity(), true)
        );
    }

    /**
     * @param NumberValue\MaximumConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumConstraint(NumberValue\MaximumConstraint $constraint)
    {
        return sprintf(
            'The number must not be more than %s.',
            var_export($constraint->maximum(), true)
        );
    }

    /**
     * @param NumberValue\MinimumConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumConstraint(NumberValue\MinimumConstraint $constraint)
    {
        return sprintf(
            'The number must not be less than %s.',
            var_export($constraint->minimum(), true)
        );
    }

    // date-time constraints ===================================================

    /**
     * @param DateTimeValue\MaximumDateTimeConstraint $constraint
     *
     * @return string
     */
    public function visitMaximumDateTimeConstraint(DateTimeValue\MaximumDateTimeConstraint $constraint)
    {
        return sprintf(
            'The date-time value must not be after %s.',
            var_export($constraint->maximum()->format('c'), true)
        );
    }

    /**
     * @param DateTimeValue\MinimumDateTimeConstraint $constraint
     *
     * @return string
     */
    public function visitMinimumDateTimeConstraint(DateTimeValue\MinimumDateTimeConstraint $constraint)
    {
        return sprintf(
            'The date-time value must not be before %s.',
            var_export($constraint->minimum()->format('c'), true)
        );
    }
}
