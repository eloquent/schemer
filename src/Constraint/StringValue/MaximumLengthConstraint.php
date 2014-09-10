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
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

/**
 * Represents a 'maxLength' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.2.1
 */
class MaximumLengthConstraint implements ConstraintInterface
{
    /**
     * Construct a new maximum length constraint.
     *
     * @param integer $maximum The maximum length.
     */
    public function __construct($maximum)
    {
        $this->maximum = $maximum;
    }

    /**
     * Get the maximum length.
     *
     * @return integer The maximum length.
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
        return $visitor->visitMaximumLengthConstraint($this);
    }

    private $maximum;
}
