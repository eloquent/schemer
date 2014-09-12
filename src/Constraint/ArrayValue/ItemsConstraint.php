<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ArrayValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\SchemaInterface;

/**
 * Represents a set of 'items' and 'additionalItems' constraints.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.3.1
 */
class ItemsConstraint implements ConstraintInterface
{
    /**
     * Construct a new items constraint.
     *
     * @param array<integer,SchemaInterface>|null $schemas          The item schemas by array index, or null if not specified.
     * @param SchemaInterface|null                $additionalSchema The schema for additional items, or null if not specified.
     */
    public function __construct(
        array $schemas = null,
        SchemaInterface $additionalSchema = null
    ) {
        $this->schemas = $schemas;
        $this->additionalSchema = $additionalSchema;
    }

    /**
     * Get the schemas.
     *
     * @return array<SchemaInterface>|null The item schemas by array index, or null if not specified.
     */
    public function schemas()
    {
        return $this->schemas;
    }

    /**
     * Get the additional item schema.
     *
     * @return SchemaInterface|null The schema for additional items, or null if not specified.
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
        return $visitor->visitItemsConstraint($this);
    }

    private $additionalSchema;
}
