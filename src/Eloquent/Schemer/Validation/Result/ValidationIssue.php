<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Validation\Result;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Value\ValueInterface;

class ValidationIssue
{
    /**
     * @param ConstraintInterface $constraint
     * @param ValueInterface      $value
     * @param PointerInterface    $pointer
     */
    public function __construct(
        ConstraintInterface $constraint,
        ValueInterface $value,
        PointerInterface $pointer
    ) {
        $this->constraint = $constraint;
        $this->value = $value;
        $this->pointer = $pointer;
    }

    /**
     * @return ConstraintInterface
     */
    public function constraint()
    {
        return $this->constraint;
    }

    /**
     * @return ValueInterface
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return PointerInterface
     */
    public function pointer()
    {
        return $this->pointer;
    }

    private $constraint;
    private $value;
    private $pointer;
}
