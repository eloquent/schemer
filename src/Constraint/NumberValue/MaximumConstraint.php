<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\NumberValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

/**
 * Represents both 'maximum' and 'exclusiveMaximum' constraints.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.1.2
 */
class MaximumConstraint implements ConstraintInterface
{
    /**
     * Construct a new maximum constraint.
     *
     * @param integer|float $maximum     The maximum value.
     * @param boolean|null  $isExclusive True if the maximum value is exclusive.
     */
    public function __construct($maximum, $isExclusive = null)
    {
        if (null === $isExclusive) {
            $isExclusive = false;
        }

        $this->maximum = $maximum;
        $this->isExclusive = $isExclusive;
    }

    /**
     * Get the maximum value.
     *
     * @return integer|float The maximum value.
     */
    public function maximum()
    {
        return $this->maximum;
    }

    /**
     * Returns true if the maximum value is exclusive.
     *
     * @return boolean True if the maximum value is exclusive.
     */
    public function isExclusive()
    {
        return $this->isExclusive;
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
        return $visitor->visitMaximumConstraint($this);
    }

    private $maximum;
    private $isExclusive;
}
