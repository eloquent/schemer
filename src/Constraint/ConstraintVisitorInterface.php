<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint;

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
use Eloquent\Schemer\Constraint\ObjectValue\MaximumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MinimumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertyDependencyConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\RequiredConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\SchemaDependencyConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\DateTimeFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\EmailFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\HostnameFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\Ipv4AddressFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\Ipv6AddressFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\UriFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\MaximumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\MinimumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\PatternConstraint;

/**
 * The interface implemented by constraint visitors.
 */
interface ConstraintVisitorInterface
{
    /**
     * Visit a schema.
     *
     * @param SchemaInterface $schema The schema to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitSchema(SchemaInterface $schema);

    // array constraints =======================================================

    /**
     * Visit an items constraint.
     *
     * @param ItemsConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitItemsConstraint(ItemsConstraint $constraint);

    /**
     * Visit a maximum items constraint.
     *
     * @param MaximumItemsConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMaximumItemsConstraint(
        MaximumItemsConstraint $constraint
    );

    /**
     * Visit a minimum items constraint.
     *
     * @param MinimumItemsConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumItemsConstraint(
        MinimumItemsConstraint $constraint
    );

    /**
     * Visit a unique items constraint.
     *
     * @param UniqueItemsConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitUniqueItemsConstraint(
        UniqueItemsConstraint $constraint
    );

    // date-time constraints ===================================================

    /**
     * Visit a maximum date-time constraint.
     *
     * @param MaximumDateTimeConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMaximumDateTimeConstraint(
        MaximumDateTimeConstraint $constraint
    );

    /**
     * Visit a minimum date-time constraint.
     *
     * @param MinimumDateTimeConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumDateTimeConstraint(
        MinimumDateTimeConstraint $constraint
    );

    // generic constraints =====================================================

    /**
     * Visit an all of constraint.
     *
     * @param AllOfConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitAllOfConstraint(AllOfConstraint $constraint);

    /**
     * Visit an any of constraint.
     *
     * @param AnyOfConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitAnyOfConstraint(AnyOfConstraint $constraint);

    /**
     * Visit an enumeration constraint.
     *
     * @param EnumerationConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitEnumerationConstraint(
        EnumerationConstraint $constraint
    );

    /**
     * Visit a not constraint.
     *
     * @param NotConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitNotConstraint(NotConstraint $constraint);

    /**
     * Visit a one of constraint.
     *
     * @param OneOfConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitOneOfConstraint(OneOfConstraint $constraint);

    /**
     * Visit a type constraint.
     *
     * @param TypeConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitTypeConstraint(TypeConstraint $constraint);

    // number constraints ======================================================

    /**
     * Visit a maximum constraint.
     *
     * @param MaximumConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMaximumConstraint(MaximumConstraint $constraint);

    /**
     * Visit a minimum constraint.
     *
     * @param MinimumConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumConstraint(MinimumConstraint $constraint);

    /**
     * Visit a multiple of constraint.
     *
     * @param MultipleOfConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMultipleOfConstraint(MultipleOfConstraint $constraint);

    // object constraints ======================================================

    /**
     * Visit a maximum properties constraint.
     *
     * @param MaximumPropertiesConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMaximumPropertiesConstraint(
        MaximumPropertiesConstraint $constraint
    );

    /**
     * Visit a minimum properties constraint.
     *
     * @param MinimumPropertiesConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumPropertiesConstraint(
        MinimumPropertiesConstraint $constraint
    );

    /**
     * Visit a required constraint.
     *
     * @param RequiredConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitRequiredConstraint(RequiredConstraint $constraint);

    /**
     * Visit a properties constraint.
     *
     * @param PropertiesConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitPropertiesConstraint(PropertiesConstraint $constraint);

    /**
     * Visit a property dependency constraint.
     *
     * @param PropertyDependencyConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitPropertyDependencyConstraint(
        PropertyDependencyConstraint $constraint
    );

    /**
     * Visit a schema dependency constraint.
     *
     * @param SchemaDependencyConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitSchemaDependencyConstraint(
        SchemaDependencyConstraint $constraint
    );

    // format constraints ======================================================

    /**
     * Visit a date-time format constraint.
     *
     * @param DateTimeFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitDateTimeFormatConstraint(
        DateTimeFormatConstraint $constraint
    );

    /**
     * Visit an email format constraint.
     *
     * @param EmailFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitEmailFormatConstraint(
        EmailFormatConstraint $constraint
    );

    /**
     * Visit a hostname format constraint.
     *
     * @param HostnameFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitHostnameFormatConstraint(
        HostnameFormatConstraint $constraint
    );

    /**
     * Visit an IPv4 format constraint.
     *
     * @param Ipv4AddressFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitIpv4AddressFormatConstraint(
        Ipv4AddressFormatConstraint $constraint
    );

    /**
     * Visit an IPv6 format constraint.
     *
     * @param Ipv6AddressFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitIpv6AddressFormatConstraint(
        Ipv6AddressFormatConstraint $constraint
    );

    /**
     * Visit a URI format constraint.
     *
     * @param UriFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitUriFormatConstraint(UriFormatConstraint $constraint);

    // string constraints ======================================================

    /**
     * Visit a maximum length constraint.
     *
     * @param MaximumLengthConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMaximumLengthConstraint(
        MaximumLengthConstraint $constraint
    );

    /**
     * Visit a minimum length constraint.
     *
     * @param MinimumLengthConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumLengthConstraint(
        MinimumLengthConstraint $constraint
    );

    /**
     * Visit a pattern constraint.
     *
     * @param PatternConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitPatternConstraint(PatternConstraint $constraint);
}
