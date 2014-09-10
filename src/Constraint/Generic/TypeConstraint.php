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
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

/**
 * Represents a 'type' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.5.2
 */
class TypeConstraint implements ConstraintInterface
{
    /**
     * Construct a new type constraint.
     *
     * @param array<string> $types The types.
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * Get the types.
     *
     * @return array<string> The types.
     */
    public function types()
    {
        return $this->types;
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
        return $visitor->visitTypeConstraint($this);
    }

    private $types;
}
