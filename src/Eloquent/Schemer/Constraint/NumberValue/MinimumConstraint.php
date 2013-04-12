<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\NumberValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

class MinimumConstraint implements ConstraintInterface
{
    /**
     * @param integer|float $minimum
     * @param boolean|null  $exclusive
     */
    public function __construct($minimum, $exclusive = null)
    {
        if (null === $exclusive) {
            $exclusive = false;
        }

        $this->minimum = $minimum;
        $this->exclusive = $exclusive;
    }

    /**
     * @return integer|float
     */
    public function minimum()
    {
        return $this->minimum;
    }

    /**
     * @return boolean
     */
    public function exclusive()
    {
        return $this->exclusive;
    }

    /**
     * @param ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitMinimumConstraint($this);
    }

    private $minimum;
    private $exclusive;
}
