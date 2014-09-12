<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ObjectValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

/**
 * Represents a 'maxProperties' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.4.1
 */
class MaximumPropertiesConstraint implements ConstraintInterface
{
    /**
     * Construct a new maximum properties constraint.
     *
     * @param integer $maximum The maximum number of properties.
     */
    public function __construct($maximum)
    {
        $this->maximum = $maximum;
    }

    /**
     * Get the maximum number of properties.
     *
     * @return integer The maximum number of properties.
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
        return $visitor->visitMaximumPropertiesConstraint($this);
    }

    private $maximum;
}
