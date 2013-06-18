<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Validation;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Value\ConcreteValueInterface;

interface ConstraintValidatorInterface
{
    /**
     * @param ConstraintInterface    $constraint
     * @param ConcreteValueInterface &$value
     * @param PointerInterface|null  $entryPoint
     *
     * @return Result\ValidationResult
     */
    public function validate(
        ConstraintInterface $constraint,
        ConcreteValueInterface &$value,
        PointerInterface $entryPoint = null
    );
}
