<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\StringValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

/**
 * Represents a 'pattern' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.2.3
 */
class PatternConstraint implements ConstraintInterface
{
    /**
     * Construct a new pattern constraint.
     *
     * @param string $pattern The pattern.
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Get the pattern.
     *
     * @return string The pattern.
     */
    public function pattern()
    {
        return $this->pattern;
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
        return $visitor->visitPatternConstraint($this);
    }

    private $pattern;
}
