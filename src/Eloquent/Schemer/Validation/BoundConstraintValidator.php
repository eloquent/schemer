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
use Eloquent\Schemer\Value\ConcreteValueInterface;

class BoundConstraintValidator implements BoundConstraintValidatorInterface
{
    /**
     * @param ConstraintInterface               $constraint
     * @param ConstraintValidatorInterface|null $validator
     */
    public function __construct(
        ConstraintInterface $constraint,
        ConstraintValidatorInterface $validator = null
    ) {
        if (null === $validator) {
            $validator = new DefaultingConstraintValidator;
        }

        $this->constraint = $constraint;
        $this->validator = $validator;
    }

    /**
     * @return ConstraintInterface
     */
    public function constraint()
    {
        return $this->constraint;
    }

    /**
     * @return ConstraintValidatorInterface
     */
    public function validator()
    {
        return $this->validator;
    }

    /**
     * @param ConcreteValueInterface $value
     *
     * @return ValidationResult
     */
    public function validate(ConcreteValueInterface $value)
    {
        return $this->validator()->validate($this->constraint(), $value);
    }

    private $validator;
    private $constraint;
}
