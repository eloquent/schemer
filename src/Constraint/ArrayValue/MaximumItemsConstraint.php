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
 * Represents a 'maxItems' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.3.2
 */
class MaximumItemsConstraint implements ConstraintInterface
{
    /**
     * Construct a new maximum items constraint.
     *
     * @param integer $maximum The maximum number of items.
     */
    public function __construct($maximum)
    {
        $this->maximum = $maximum;
    }

    /**
     * Get the maximum number of items.
     *
     * @return integer The maximum number of items.
     */
    public function maximum()
    {
        return $this->maximum;
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
        return $visitor->visitMaximumItemsConstraint($this);
    }

    private $maximum;
}
