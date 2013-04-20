<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ArrayValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

class MaximumItemsConstraint implements ConstraintInterface
{
    /**
     * @param integer $maximum
     */
    public function __construct($maximum)
    {
        $this->maximum = $maximum;
    }

    /**
     * @return integer
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
        return $visitor->visitMaximumItemsConstraint($this);
    }

    private $maximum;
}
