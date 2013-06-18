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

class DefaultingConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * @param ConstraintValidatorInterface|null $validator
     * @param DefaultValueTransformFactory|null $defaultTransformFactory
     */
    public function __construct(
        ConstraintValidatorInterface $validator = null,
        DefaultValueTransformFactory $defaultTransformFactory = null
    ) {
        if (null === $validator) {
            $validator = new ConstraintValidator;
        }
        if (null === $defaultTransformFactory) {
            $defaultTransformFactory = new DefaultValueTransformFactory;
        }

        $this->validator = $validator;
        $this->defaultTransformFactory = $defaultTransformFactory;
    }

    /**
     * @return ConstraintValidatorInterface
     */
    public function validator()
    {
        return $this->validator;
    }

    /**
     * @return DefaultValueTransformFactory
     */
    public function defaultTransformFactory()
    {
        return $this->defaultTransformFactory;
    }

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
    ) {
        $result = $this->validator()->validate($constraint, $value, $entryPoint);
        $value = $this->defaultTransformFactory()
            ->create($result)
            ->transform($value);

        return $result;
    }

    private $validator;
    private $defaultTransformFactory;
}
