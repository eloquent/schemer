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
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

/**
 * Represents a 'minProperties' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.4.2
 */
class MinimumPropertiesConstraint implements ConstraintInterface
{
    /**
     * Construct a new minimum properties constraint.
     *
     * @param integer $minimum The minimum number of properties.
     */
    public function __construct($minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * Get the minimum number of properties.
     *
     * @return integer The minimum number of properties.
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
        return $visitor->visitMinimumPropertiesConstraint($this);
    }

    private $minimum;
}
