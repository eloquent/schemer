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

use Eloquent\Schemer\Constraint\AbstractSchemaContainerConstraint;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\SchemaInterface;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

/**
 * Represents a set of 'items' and 'additionalItems' constraints.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.3.1
 */
class ItemsConstraint extends AbstractSchemaContainerConstraint
{
    /**
     * Construct a new items constraint.
     *
     * @param array<integer,SchemaInterface>|null $schemas          The item schemas by array index.
     * @param SchemaInterface|null                $additionalSchema The schema for additional items.
     */
    public function __construct(
        array $schemas = null,
        SchemaInterface $additionalSchema = null
    ) {
        if (null === $schemas) {
            $schemas = [];
        }
        if (null === $additionalSchema) {
            $additionalSchema = Schema::createEmpty();
        }

        parent::__construct($schemas);

        $this->additionalSchema = $additionalSchema;
    }

    /**
     * Get the additional item schema.
     *
     * @return SchemaInterface The schema for additional items.
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
