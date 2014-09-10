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
 * Represents a 'multipleOf' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.1.1
 */
class MultipleOfConstraint implements ConstraintInterface
{
    /**
     * Construct a new multiple of constraint.
     *
     * @param integer|float $quantity The quantity.
     */
    public function __construct($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Get the quantity.
     *
     * @return integer|float The quantity.
     */
    public function quantity()
    {
        return $this->quantity;
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
        return $visitor->visitMultipleOfConstraint($this);
    }

    private $quantity;
}
