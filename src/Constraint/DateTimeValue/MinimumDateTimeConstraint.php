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
 * Represents a minimum date-time constraint.
 *
 * This constraint has no equivalent in the JSON Schema specification.
 */
class MinimumDateTimeConstraint implements ConstraintInterface
{
    /**
     * Construct a new minimum date-time constraint.
     *
     * @param DateTime     $minimum     The minimum date-time.
     * @param boolean|null $isExclusive True if the minimum value is exclusive.
     */
    public function __construct(DateTime $minimum, $isExclusive = null)
    {
        if (null === $isExclusive) {
            $isExclusive = false;
        }

        $this->minimum = $minimum;
        $this->isExclusive = $isExclusive;
    }

    /**
     * Get the minimum date-time.
     *
     * @return DateTime The minimum date-time.
     */
    public function minimum()
    {
        return $this->minimum;
    }

    /**
     * Returns true if the minimum value is exclusive.
     *
     * @return boolean True if the minimum value is exclusive.
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
        return $visitor->visitMinimumDateTimeConstraint($this);
    }

    private $minimum;
    private $isExclusive;
}
