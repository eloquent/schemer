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
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\SchemaInterface;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

/**
 * Represents an individual schema dependency constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.4.5
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.4.5.2.1
 */
class SchemaDependencyConstraint implements ConstraintInterface
{
    /**
     * Construct a new schema dependency constraint.
     *
     * @param string               $property The property.
     * @param SchemaInterface|null $schema   The schema.
     */
    public function __construct($property, SchemaInterface $schema = null)
    {
        if (null === $schema) {
            $schema = Schema::createEmpty();
        }

        $this->property = $property;
        $this->schema = $schema;
    }

    /**
     * Get the property.
     *
     * @return string The property.
     */
    public function property()
    {
        return $this->property;
    }

    /**
     * Get the schema.
     *
     * @return SchemaInterface The schema.
     */
    public function schema()
    {
        return $this->schema;
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
        return $visitor->visitSchemaDependencyConstraint($this);
    }

    private $property;
    private $schema;
}
