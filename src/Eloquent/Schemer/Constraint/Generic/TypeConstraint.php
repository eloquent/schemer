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
use Eloquent\Schemer\Value\ValueType;

class TypeConstraint implements ConstraintInterface
{
    /**
     * @param array<ValueType> $valueTypes
     */
    public function __construct(array $valueTypes)
    {
        $this->valueTypes = $valueTypes;
    }

    /**
     * @return array<ValueType>
     */
    public function valueTypes()
    {
        return $this->valueTypes;
    }

    /**
     * @param ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitTypeConstraint($this);
    }

    private $valueTypes;
}
