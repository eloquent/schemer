<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\StringValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

class MinimumLengthConstraint implements ConstraintInterface
{
    /**
     * @param integer $minimum
     */
    public function __construct($minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * @return integer
     */
    public function minimum()
    {
        return $this->minimum;
    }

    /**
     * @param ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitMinimumLengthConstraint($this);
    }

    private $minimum;
}
