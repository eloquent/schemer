<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Transform;

use Eloquent\Schemer\Constraint\ArrayValue;
use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\DateTimeValue;
use Eloquent\Schemer\Constraint\Generic;
use Eloquent\Schemer\Constraint\NumberValue;
use Eloquent\Schemer\Constraint\ObjectValue;
use Eloquent\Schemer\Constraint\PlaceholderSchema;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\StringValue;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

abstract class AbstractConstraintTransform implements
    ConstraintTransformInterface,
    ConstraintVisitorInterface
{
    public function __construct()
    {
        $this->clear();
    }

    /**
     * @param ConstraintInterface $constraint
     *
     * @return ConstraintInterface
     */
    public function transform(ConstraintInterface $constraint)
    {
        $this->clear();
        $this->setConstraint($constraint);
        $constraint = $constraint->accept($this);
        $this->clear();

        return $constraint;
    }

    /**
     * @param Schema $schema
     *
     * @return Schema
     */
    public function visitSchema(Schema $schema)
    {
        return new Schema(
            $this->transformConstraintArray($schema->constraints()),
            $schema->defaultValue(),
            $schema->title(),
            $schema->description()
        );
    }

    /**
     * @param PlaceholderSchema $schema
     *
     * @return PlaceholderSchema
     */
    public function visitPlaceholderSchema(PlaceholderSchema $schema)
    {
        return $schema;
    }

    // generic constraints =====================================================

    /**
     * @param Generic\EnumConstraint $constraint
     *
     * @return Generic\EnumConstraint
     */
    public function visitEnumConstraint(Generic\EnumConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param Generic\TypeConstraint $constraint
     *
     * @return Generic\TypeConstraint
     */
    public function visitTypeConstraint(Generic\TypeConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param Generic\AllOfConstraint $constraint
     *
     * @return Generic\AllOfConstraint
     */
    public function visitAllOfConstraint(Generic\AllOfConstraint $constraint)
    {
        return new Generic\AllOfConstraint(
            $this->transformConstraintArray($constraint->schemas())
        );
    }

    /**
     * @param Generic\AnyOfConstraint $constraint
     *
     * @return Generic\AnyOfConstraint
     */
    public function visitAnyOfConstraint(Generic\AnyOfConstraint $constraint)
    {
        return new Generic\AnyOfConstraint(
            $this->transformConstraintArray($constraint->schemas())
        );
    }

    /**
     * @param Generic\OneOfConstraint $constraint
     *
     * @return Generic\OneOfConstraint
     */
    public function visitOneOfConstraint(Generic\OneOfConstraint $constraint)
    {
        return new Generic\OneOfConstraint(
            $this->transformConstraintArray($constraint->schemas())
        );
    }

    /**
     * @param Generic\NotConstraint $constraint
     *
     * @return Generic\NotConstraint
     */
    public function visitNotConstraint(Generic\NotConstraint $constraint)
    {
        return new Generic\NotConstraint($constraint->schema()->accept($this));
    }

    // object constraints ======================================================

    /**
     * @param ObjectValue\MaximumPropertiesConstraint $constraint
     *
     * @return ObjectValue\MaximumPropertiesConstraint
     */
    public function visitMaximumPropertiesConstraint(ObjectValue\MaximumPropertiesConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param ObjectValue\MinimumPropertiesConstraint $constraint
     *
     * @return ObjectValue\MinimumPropertiesConstraint
     */
    public function visitMinimumPropertiesConstraint(ObjectValue\MinimumPropertiesConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param ObjectValue\RequiredConstraint $constraint
     *
     * @return ObjectValue\RequiredConstraint
     */
    public function visitRequiredConstraint(ObjectValue\RequiredConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param ObjectValue\PropertiesConstraint $constraint
     *
     * @return ObjectValue\PropertiesConstraint
     */
    public function visitPropertiesConstraint(ObjectValue\PropertiesConstraint $constraint)
    {
        return new ObjectValue\PropertiesConstraint(
            $this->transformConstraintArray($constraint->schemas()),
            $this->transformConstraintArray($constraint->patternSchemas()),
            $constraint->additionalSchema()->accept($this)
        );
    }

    /**
     * @param ObjectValue\AdditionalPropertyConstraint $constraint
     *
     * @return ObjectValue\AdditionalPropertyConstraint
     */
    public function visitAdditionalPropertyConstraint(ObjectValue\AdditionalPropertyConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param ObjectValue\DependencyConstraint $constraint
     *
     * @return ObjectValue\DependencyConstraint
     */
    public function visitDependencyConstraint(ObjectValue\DependencyConstraint $constraint)
    {
        return new ObjectValue\DependencyConstraint(
            $constraint->property(),
            $constraint->schema()->accept($this)
        );
    }

    // array constraints =======================================================

    /**
     * @param ArrayValue\ItemsConstraint $constraint
     *
     * @return ArrayValue\ItemsConstraint
     */
    public function visitItemsConstraint(ArrayValue\ItemsConstraint $constraint)
    {
        return new ArrayValue\ItemsConstraint(
            $this->transformConstraintArray($constraint->schemas()),
            $constraint->additionalSchema()->accept($this)
        );
    }

    /**
     * @param ArrayValue\AdditionalItemConstraint $constraint
     *
     * @return ArrayValue\AdditionalItemConstraint
     */
    public function visitAdditionalItemConstraint(ArrayValue\AdditionalItemConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param ArrayValue\MaximumItemsConstraint $constraint
     *
     * @return ArrayValue\MaximumItemsConstraint
     */
    public function visitMaximumItemsConstraint(ArrayValue\MaximumItemsConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param ArrayValue\MinimumItemsConstraint $constraint
     *
     * @return ArrayValue\MinimumItemsConstraint
     */
    public function visitMinimumItemsConstraint(ArrayValue\MinimumItemsConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param ArrayValue\UniqueItemsConstraint $constraint
     *
     * @return ArrayValue\UniqueItemsConstraint
     */
    public function visitUniqueItemsConstraint(ArrayValue\UniqueItemsConstraint $constraint)
    {
        return $constraint;
    }

    // string constraints ======================================================

    /**
     * @param StringValue\PatternConstraint $constraint
     *
     * @return StringValue\PatternConstraint
     */
    public function visitPatternConstraint(StringValue\PatternConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param StringValue\MaximumLengthConstraint $constraint
     *
     * @return StringValue\MaximumLengthConstraint
     */
    public function visitMaximumLengthConstraint(StringValue\MaximumLengthConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param StringValue\MinimumLengthConstraint $constraint
     *
     * @return StringValue\MinimumLengthConstraint
     */
    public function visitMinimumLengthConstraint(StringValue\MinimumLengthConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param StringValue\DateTimeFormatConstraint $constraint
     *
     * @return StringValue\DateTimeFormatConstraint
     */
    public function visitDateTimeFormatConstraint(StringValue\DateTimeFormatConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param StringValue\EmailFormatConstraint $constraint
     *
     * @return StringValue\EmailFormatConstraint
     */
    public function visitEmailFormatConstraint(StringValue\EmailFormatConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param StringValue\HostnameFormatConstraint $constraint
     *
     * @return StringValue\HostnameFormatConstraint
     */
    public function visitHostnameFormatConstraint(StringValue\HostnameFormatConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param StringValue\Ipv4AddressFormatConstraint $constraint
     *
     * @return StringValue\Ipv4AddressFormatConstraint
     */
    public function visitIpv4AddressFormatConstraint(StringValue\Ipv4AddressFormatConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param StringValue\Ipv6AddressFormatConstraint $constraint
     *
     * @return StringValue\Ipv6AddressFormatConstraint
     */
    public function visitIpv6AddressFormatConstraint(StringValue\Ipv6AddressFormatConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param StringValue\UriFormatConstraint $constraint
     *
     * @return StringValue\UriFormatConstraint
     */
    public function visitUriFormatConstraint(StringValue\UriFormatConstraint $constraint)
    {
        return $constraint;
    }

    // number constraints ======================================================

    /**
     * @param NumberValue\MultipleOfConstraint $constraint
     *
     * @return NumberValue\MultipleOfConstraint
     */
    public function visitMultipleOfConstraint(NumberValue\MultipleOfConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param NumberValue\MaximumConstraint $constraint
     *
     * @return NumberValue\MaximumConstraint
     */
    public function visitMaximumConstraint(NumberValue\MaximumConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param NumberValue\MinimumConstraint $constraint
     *
     * @return NumberValue\MinimumConstraint
     */
    public function visitMinimumConstraint(NumberValue\MinimumConstraint $constraint)
    {
        return $constraint;
    }

    // date-time constraints ===================================================

    /**
     * @param DateTimeValue\MaximumDateTimeConstraint $constraint
     *
     * @return DateTimeValue\MaximumDateTimeConstraint
     */
    public function visitMaximumDateTimeConstraint(DateTimeValue\MaximumDateTimeConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param DateTimeValue\MinimumDateTimeConstraint $constraint
     *
     * @return DateTimeValue\MinimumDateTimeConstraint
     */
    public function visitMinimumDateTimeConstraint(DateTimeValue\MinimumDateTimeConstraint $constraint)
    {
        return $constraint;
    }

    /**
     * @param array<ConstraintInterface> $constraints
     *
     * @return array<ConstraintInterface>
     */
    protected function transformConstraintArray(array $constraints)
    {
        $self = $this;

        return array_map(
            function (ConstraintInterface $constraint) use ($self) {
                return $constraint->accept($self);
            },
            $constraints
        );
    }

    protected function clear()
    {
        $this->setConstraint(null);
    }

    /**
     * @param ConstraintInterface|null $constraint
     */
    protected function setConstraint(ConstraintInterface $constraint = null)
    {
        $this->constraint = $constraint;
    }

    /**
     * @return ConstraintInterface|null
     */
    protected function constraint()
    {
        return $this->constraint;
    }

    private $constraint;
}
