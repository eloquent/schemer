<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Generic;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Value\ArrayValue;

class EnumConstraint implements ConstraintInterface
{
    /**
     * @param ArrayValue $values
     */
    public function __construct(ArrayValue $values)
    {
        $this->values = $values;
    }

    /**
     * @return ArrayValue
     */
    public function values()
    {
        return $this->values;
    }

    /**
     * @param ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitEnumConstraint($this);
    }

    private $values;
}
