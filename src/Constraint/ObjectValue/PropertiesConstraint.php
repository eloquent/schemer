<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ObjectValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\SchemaInterface;

/**
 * Represents a set of 'properties', 'patternProperties', and
 * 'additionalProperties' constraints.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.3.1
 */
class PropertiesConstraint implements ConstraintInterface
{
    /**
     * Construct a new properties constraint.
     *
     * @param array<string,SchemaInterface>|null $schemas          A map of property to schema, or null if not specified.
     * @param array<string,SchemaInterface>|null $patternSchemas   A map of pattern to schema, or null if not specified.
     * @param SchemaInterface|null               $additionalSchema The schema for additional properties, or null if not specified.
     */
    public function __construct(
        array $schemas = null,
        array $patternSchemas = null,
        SchemaInterface $additionalSchema = null
    ) {
        $this->schemas = $schemas;
        $this->patternSchemas = $patternSchemas;
        $this->additionalSchema = $additionalSchema;
    }

    /**
     * Get the property schemas.
     *
     * @return array<string,SchemaInterface>|null A map of property to schema, or null if not specified.
     */
    public function schemas()
    {
        return $this->schemas;
    }

    /**
     * Get the pattern schemas.
     *
     * @return array<string,SchemaInterface>|null A map of pattern to schema, or null if not specified.
     */
    public function patternSchemas()
    {
        return $this->patternSchemas;
    }

    /**
     * Get the additional property schema.
     *
     * @return SchemaInterface|null The schema for additional properties, or null if not specified.
     */
    public function additionalSchema()
    {
        return $this->additionalSchema;
    }

    /**
     * Accept the supplied visitor.
     *
     * @param ConstraintVisitorInterface $visitor The visitor to accept.
     *
     * @return mixed The result of visitation.
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitPropertiesConstraint($this);
    }

    private $schemas;
    private $patternSchemas;
    private $additionalSchema;
}