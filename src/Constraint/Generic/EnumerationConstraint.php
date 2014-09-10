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
 * Represents an 'enum' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.5.1
 */
class EnumerationConstraint implements ConstraintInterface
{
    /**
     * Construct a new enumeration constraint.
     *
     * @param array $members The enumeration members.
     */
    public function __construct(array $members)
    {
        $this->members = $members;
    }

    /**
     * Get the enumeration members.
     *
     * @return array The enumeration members.
     */
    public function members()
    {
        return $this->members;
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
        return $visitor->visitEnumerationConstraint($this);
    }

    private $members;
}
