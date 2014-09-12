<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Generic;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\SchemaInterface;

/**
 * Represents a 'not' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.5.6
 */
class NotConstraint implements ConstraintInterface
{
    /**
     * Construct a new not constraint.
     *
     * @param SchemaInterface|null $schema The schema.
     */
    public function __construct(SchemaInterface $schema = null)
    {
        if (null === $schema) {
            $schema = Schema::createEmpty();
        }

        $this->schema = $schema;
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
        return $visitor->visitNotConstraint($this);
    }

    private $schema;
}
