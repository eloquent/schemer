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
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

class UniqueItemsConstraint implements ConstraintInterface
{
    /**
     * @param boolean $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return boolean
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @param ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitUniqueItemsConstraint($this);
    }

    private $value;
}
