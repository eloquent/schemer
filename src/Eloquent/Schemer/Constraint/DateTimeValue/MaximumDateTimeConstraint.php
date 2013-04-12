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

class MaximumDateTimeConstraint implements ConstraintInterface
{
    /**
     * @param DateTime $maximum
     */
    public function __construct(DateTime $maximum)
    {
        $this->maximum = $maximum;
    }

    /**
     * @return DateTime
     */
    public function maximum()
    {
        return $this->maximum;
    }

    /**
     * @param ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitMaximumDateTimeConstraint($this);
    }

    private $maximum;
}
