<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\DateTimeValue;

use DateTime;
use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

class MinimumDateTimeConstraint implements ConstraintInterface
{
    /**
     * @param DateTime $minimum
     */
    public function __construct(DateTime $minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * @return DateTime
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
        return $visitor->visitMinimumDateTimeConstraint($this);
    }

    private $minimum;
}
