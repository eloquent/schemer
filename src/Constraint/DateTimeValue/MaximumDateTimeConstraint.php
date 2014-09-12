<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\DateTimeValue;

use DateTime;
use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

/**
 * Represents a maximum date-time constraint.
 *
 * This constraint has no equivalent in the JSON Schema specification.
 */
class MaximumDateTimeConstraint implements ConstraintInterface
{
    /**
     * Construct a new maximum date-time constraint.
     *
     * @param DateTime     $maximum     The maximum date-time.
     * @param boolean|null $isExclusive True if the maximum value is exclusive.
     */
    public function __construct(DateTime $maximum, $isExclusive = null)
    {
        if (null === $isExclusive) {
            $isExclusive = false;
        }

        $this->maximum = $maximum;
        $this->isExclusive = $isExclusive;
    }

    /**
     * Get the maximum date-time.
     *
     * @return DateTime The maximum date-time.
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
        return $visitor->visitMaximumDateTimeConstraint($this);
    }

    private $maximum;
    private $isExclusive;
}
