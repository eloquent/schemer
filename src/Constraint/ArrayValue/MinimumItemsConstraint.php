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

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

/**
 * Represents a 'minItems' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.3.3
 */
class MinimumItemsConstraint implements ConstraintInterface
{
    /**
     * Construct a new minimum items constraint.
     *
     * @param integer $minimum The minimum number of items.
     */
    public function __construct($minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * Get the minimum number of items.
     *
     * @return integer The minimum number of items.
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
        return $visitor->visitMinimumItemsConstraint($this);
    }

    private $minimum;
}
