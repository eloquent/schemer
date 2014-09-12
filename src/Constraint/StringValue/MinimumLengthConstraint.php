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
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

/**
 * Represents a 'minLength' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.2.2
 */
class MinimumLengthConstraint implements ConstraintInterface
{
    /**
     * Construct a new minimum length constraint.
     *
     * @param integer $minimum The minimum length.
     */
    public function __construct($minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * Get the minimum length.
     *
     * @return integer The minimum length.
     */
    public function minimum()
    {
        return $this->minimum;
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
        return $visitor->visitMinimumLengthConstraint($this);
    }

    private $minimum;
}
