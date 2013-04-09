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
use Eloquent\Schemer\Value\ValueInterface;

class BoundConstraintValidator implements BoundConstraintValidatorInterface
{
    /**
     * @param ConstraintValidatorInterface $validator
     * @param ConstraintInterface          $constraint
     */
    public function __construct(
        ConstraintValidatorInterface $validator,
        ConstraintInterface $constraint
    ) {
        $this->validator = $validator;
        $this->constraint = $constraint;
    }

    /**
     * @return ConstraintValidatorInterface
     */
    public function validator()
    {
        return $this->validator;
    }

    /**
     * @return ConstraintInterface
     */
    public function constraint()
    {
        return $this->constraint;
    }

    /**
     * @param ValueInterface $value
     *
     * @return ValidationResult
     */
    public function validate(ValueInterface $value)
    {
        return $this->validator()->validate($this->constraint(), $value);
    }

    private $validator;
    private $constraint;
}
