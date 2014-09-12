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
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

/**
 * Represents both 'minimum' and 'exclusiveMinimum' constraints.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.1.3
 */
class MinimumConstraint implements ConstraintInterface
{
    /**
     * Construct a new minimum constraint.
     *
     * @param integer|float $minimum     The minimum value.
     * @param boolean|null  $isExclusive True if the minimum value is exclusive.
     */
    public function __construct($minimum, $isExclusive = null)
    {
        if (null === $isExclusive) {
            $isExclusive = false;
        }

        $this->minimum = $minimum;
        $this->isExclusive = $isExclusive;
    }

    /**
     * Get the minimum value.
     *
     * @return integer|float The minimum value.
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
        return $visitor->visitMinimumConstraint($this);
    }

    private $minimum;
    private $isExclusive;
}
