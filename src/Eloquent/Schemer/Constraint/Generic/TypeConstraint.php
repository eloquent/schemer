<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\GenericConstraint;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Value\ValueType;

class TypeConstraint implements ConstraintInterface
{
    /**
     * @param ValueType $type
     */
    public function __construct(ValueType $type)
    {
        $this->type = $type;
    }

    /**
     * @return ValueType
     */
    public function type()
    {
        return $this->type;
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

    private $type;
}
