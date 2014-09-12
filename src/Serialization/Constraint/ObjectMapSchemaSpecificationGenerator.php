<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Constraint;

use Eloquent\Schemer\Constraint\ArrayValue\ItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MaximumItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MinimumItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\UniqueItemsConstraint;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
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
use Eloquent\Schemer\Constraint\ObjectValue\DependenciesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MaximumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MinimumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\RequiredConstraint;
use Eloquent\Schemer\Constraint\SchemaInterface;
use Eloquent\Schemer\Constraint\StringValue\Format\DateTimeFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\EmailFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\HostnameFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\Ipv4AddressFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\Ipv6AddressFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Format\UriFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\MaximumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\MinimumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\PatternConstraint;
use SplObjectStorage;
use stdClass;

/**
 * Generates schema specifications using object maps (stdClass objects).
 */
class ObjectMapSchemaSpecificationGenerator implements
    SchemaSpecificationGeneratorInterface,
    ConstraintVisitorInterface
{
    /**
     * Get a static object map schema specification generator instance.
     *
     * @return SchemaSpecificationGeneratorInterface The static object map schema specification generator instance.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Construct a new object map schema specification generator.
     */
    public function __construct()
    {
        $this->schemaSpecifications = new SplObjectStorage;
        $this->seenSchemas = new SplObjectStorage;
        $this->queue = [];
    }

    /**
     * Transform the supplied schema into a schema specification.
     *
     * @param SchemaInterface $schema The schema.
     *
     * @return mixed The schema specification.
     */
    public function schemaToSpecification(SchemaInterface $schema)
    {
        $specification = $this->schemaSpecification($schema);
        array_push($this->queue, [$schema, $specification]);

        while ($this->queue) {
            list($this->subject, $this->specification) =
                array_shift($this->queue);

            $this->subject->accept($this);
        }

        $this->schemaSpecifications = new SplObjectStorage;
        $this->seenSchemas = new SplObjectStorage;
        $this->queue = [];
        $this->subject = null;
        $this->specification = null;

        return $specification;
    }

    /**
     * Visit a schema.
     *
     * @param SchemaInterface $schema The schema to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitSchema(SchemaInterface $schema)
    {
        if ($this->seenSchemas->contains($schema)) {
            return;
        }

        $this->seenSchemas->attach($schema);

        if (null !== $schema->title()) {
            $this->specification->title = $schema->title();
        }
        if (null !== $schema->description()) {
            $this->specification->description = $schema->description();
        }
        if (null !== $schema->defaultValue()) {
            $this->specification->defaultValue = $schema->defaultValue();
        }

        foreach ($schema->constraints() as $constraint) {
            array_push($this->queue, [$constraint, $this->specification]);
        }
    }

    // array constraints =======================================================

    /**
     * Visit an items constraint.
     *
     * @param ItemsConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitItemsConstraint(ItemsConstraint $constraint)
    {
        $schemas = $constraint->schemas();
        if (null !== $schemas) {
            $this->specification->items = [];
            $this->queueSchemaArray($schemas, $this->specification->items);
        }

        $additionalSchema = $constraint->additionalSchema();
        if (null !== $additionalSchema) {
            $specification = $this->schemaSpecification($additionalSchema);
            $this->specification->additionalItems = $specification;
            array_push($this->queue, [$additionalSchema, $specification]);
        }
    }

    /**
     * Visit a maximum items constraint.
     *
     * @param MaximumItemsConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMaximumItemsConstraint(
        MaximumItemsConstraint $constraint
    ) {
        $this->specification->maxItems = $constraint->maximum();
    }

    /**
     * Visit a minimum items constraint.
     *
     * @param MinimumItemsConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumItemsConstraint(
        MinimumItemsConstraint $constraint
    ) {
        $this->specification->minItems = $constraint->minimum();
    }

    /**
     * Visit a unique items constraint.
     *
     * @param UniqueItemsConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitUniqueItemsConstraint(
        UniqueItemsConstraint $constraint
    ) {
        $this->specification->uniqueItems = $constraint->isUnique();
    }

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
    ) {
        $this->specification->maxDateTime = $constraint->maximum();
    }

    /**
     * Visit a minimum date-time constraint.
     *
     * @param MinimumDateTimeConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumDateTimeConstraint(
        MinimumDateTimeConstraint $constraint
    ) {
        $this->specification->minDateTime = $constraint->minimum();
    }

    // generic constraints =====================================================

    /**
     * Visit an all of constraint.
     *
     * @param AllOfConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitAllOfConstraint(AllOfConstraint $constraint)
    {
        $this->specification->allOf = [];
        $this->queueSchemaArray(
            $constraint->schemas(),
            $this->specification->allOf
        );
    }

    /**
     * Visit an any of constraint.
     *
     * @param AnyOfConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitAnyOfConstraint(AnyOfConstraint $constraint)
    {
        $this->specification->anyOf = [];
        $this->queueSchemaArray(
            $constraint->schemas(),
            $this->specification->anyOf
        );
    }

    /**
     * Visit an enumeration constraint.
     *
     * @param EnumerationConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitEnumerationConstraint(
        EnumerationConstraint $constraint
    ) {
        $this->specification->enum = $constraint->members();
    }

    /**
     * Visit a not constraint.
     *
     * @param NotConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitNotConstraint(NotConstraint $constraint)
    {
        $schema = $constraint->schema();
        $specification = $this->schemaSpecification($schema);
        $this->specification->not = $specification;
        array_push($this->queue, [$schema, $specification]);
    }

    /**
     * Visit a one of constraint.
     *
     * @param OneOfConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitOneOfConstraint(OneOfConstraint $constraint)
    {
        $this->specification->oneOf = [];
        $this->queueSchemaArray(
            $constraint->schemas(),
            $this->specification->oneOf
        );
    }

    /**
     * Visit a type constraint.
     *
     * @param TypeConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitTypeConstraint(TypeConstraint $constraint)
    {
        $this->specification->type = $constraint->types();
    }

    // number constraints ======================================================

    /**
     * Visit a maximum constraint.
     *
     * @param MaximumConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMaximumConstraint(MaximumConstraint $constraint)
    {
        $this->specification->maximum = $constraint->maximum();
    }

    /**
     * Visit a minimum constraint.
     *
     * @param MinimumConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumConstraint(MinimumConstraint $constraint)
    {
        $this->specification->minimum = $constraint->minimum();
    }

    /**
     * Visit a multiple of constraint.
     *
     * @param MultipleOfConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMultipleOfConstraint(MultipleOfConstraint $constraint)
    {
        $this->specification->multipleOf = $constraint->quantity();
    }

    // object constraints ======================================================

    /**
     * Visit a dependencies constraint.
     *
     * @param DependenciesConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitDependenciesConstraint(
        DependenciesConstraint $constraint
    ) {
        $this->specification->dependencies = new stdClass;

        foreach ($constraint->dependencies() as $property => $dependency) {
            if ($dependency instanceof SchemaInterface) {
                $specification = $this->schemaSpecification($dependency);
                $this->specification->dependencies->$property = $specification;
                array_push($this->queue, [$dependency, $specification]);
            } else {
                $this->specification->dependencies->$property = $dependency;
            }
        }
    }

    /**
     * Visit a maximum properties constraint.
     *
     * @param MaximumPropertiesConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMaximumPropertiesConstraint(
        MaximumPropertiesConstraint $constraint
    ) {
        $this->specification->maxProperties = $constraint->maximum();
    }

    /**
     * Visit a minimum properties constraint.
     *
     * @param MinimumPropertiesConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumPropertiesConstraint(
        MinimumPropertiesConstraint $constraint
    ) {
        $this->specification->minProperties = $constraint->minimum();
    }

    /**
     * Visit a properties constraint.
     *
     * @param PropertiesConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitPropertiesConstraint(PropertiesConstraint $constraint)
    {
        $schemas = $constraint->schemas();
        if (null !== $schemas) {
            $this->specification->properties = new stdClass;
            $this->queueSchemaMap($schemas, $this->specification->properties);
        }

        $schemas = $constraint->patternSchemas();
        if (null !== $schemas) {
            $this->specification->patternProperties = new stdClass;
            $this->queueSchemaMap(
                $schemas,
                $this->specification->patternProperties
            );
        }

        $additionalSchema = $constraint->additionalSchema();
        if (null !== $additionalSchema) {
            $specification = $this->schemaSpecification($additionalSchema);
            $this->specification->additionalProperties = $specification;
            array_push($this->queue, [$additionalSchema, $specification]);
        }
    }

    /**
     * Visit a required constraint.
     *
     * @param RequiredConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitRequiredConstraint(RequiredConstraint $constraint)
    {
        $this->specification->required = $constraint->properties();
    }

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
    ) {
        $this->specification->format = 'date-time';
    }

    /**
     * Visit an email format constraint.
     *
     * @param EmailFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitEmailFormatConstraint(
        EmailFormatConstraint $constraint
    ) {
        $this->specification->format = 'email';
    }

    /**
     * Visit a hostname format constraint.
     *
     * @param HostnameFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitHostnameFormatConstraint(
        HostnameFormatConstraint $constraint
    ) {
        $this->specification->format = 'hostname';
    }

    /**
     * Visit an IPv4 format constraint.
     *
     * @param Ipv4AddressFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitIpv4AddressFormatConstraint(
        Ipv4AddressFormatConstraint $constraint
    ) {
        $this->specification->format = 'ipv4';
    }

    /**
     * Visit an IPv6 format constraint.
     *
     * @param Ipv6AddressFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitIpv6AddressFormatConstraint(
        Ipv6AddressFormatConstraint $constraint
    ) {
        $this->specification->format = 'ipv6';
    }

    /**
     * Visit a URI format constraint.
     *
     * @param UriFormatConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitUriFormatConstraint(UriFormatConstraint $constraint)
    {
        $this->specification->format = 'uri';
    }

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
    ) {
        $this->specification->maxLength = $constraint->maximum();
    }

    /**
     * Visit a minimum length constraint.
     *
     * @param MinimumLengthConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitMinimumLengthConstraint(
        MinimumLengthConstraint $constraint
    ) {
        $this->specification->minLength = $constraint->minimum();
    }

    /**
     * Visit a pattern constraint.
     *
     * @param PatternConstraint $constraint The constraint to visit.
     *
     * @return mixed The result of visitation.
     */
    public function visitPatternConstraint(PatternConstraint $constraint)
    {
        $this->specification->pattern = $constraint->pattern();
    }

    /**
     * Retrieve or create a specification object for the supplied schema.
     *
     * @param SchemaInterface $schema The schema.
     *
     * @return stdClass The specification object.
     */
    protected function schemaSpecification(SchemaInterface $schema)
    {
        if ($this->schemaSpecifications->contains($schema)) {
            $specification = $this->schemaSpecifications->offsetGet($schema);
        } else {
            $specification = new stdClass;
            $this->schemaSpecifications->attach($schema, $specification);
        }

        return $specification;
    }

    /**
     * Queue an array of schemas for visitation.
     *
     * @param array<SchemaInterface> $schemas       The schemas.
     * @param array                  $specification The specification array.
     */
    protected function queueSchemaArray(array $schemas, array &$specification)
    {
        foreach ($schemas as $schema) {
            $schemaSpecification = $this->schemaSpecification($schema);
            $specification[] = $schemaSpecification;
            array_push($this->queue, [$schema, $schemaSpecification]);
        }
    }

    /**
     * Queue a map of schemas for visitation.
     *
     * @param array<string,SchemaInterface> $schemas       The schemas.
     * @param stdClass                      $specification The specification array.
     */
    protected function queueSchemaMap(array $schemas, stdClass &$specification)
    {
        foreach ($schemas as $key => $schema) {
            $schemaSpecification = $this->schemaSpecification($schema);
            $specification->$key = $schemaSpecification;
            array_push($this->queue, [$schema, $schemaSpecification]);
        }
    }

    private static $instance;
    private $schemaSpecifications;
    private $seenSchemas;
    private $queue;
    private $subject;
    private $specification;
}
