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

class DefaultingConstraintValidator implements
    DefaultingConstraintValidatorInterface
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
     * @param ConcreteValueInterface $value
     * @param PointerInterface|null  $entryPoint
     *
     * @return ValidationResult
     */
    public function validate(
        ConstraintInterface $constraint,
        ConcreteValueInterface $value,
        PointerInterface $entryPoint = null
    ) {
        return $this->validator()->validate($constraint, $value, $entryPoint);
    }

    /**
     * @param ConstraintInterface    $constraint
     * @param ConcreteValueInterface &$value
     * @param PointerInterface|null  $entryPoint
     *
     * @return Result\ValidationResult
     */
    public function validateAndApplyDefaults(
        ConstraintInterface $constraint,
        ConcreteValueInterface &$value,
        PointerInterface $entryPoint = null
    ) {
        $result = $this->validate($constraint, $value, $entryPoint);
        $value = $this->applyDefaults($result, $value);

        return $result;
    }

    /**
     * @param Result\ValidationResult $result
     * @param ConcreteValueInterface  $value
     *
     * @return ConcreteValueInterface
     */
    public function applyDefaults(
        Result\ValidationResult $result,
        ConcreteValueInterface $value
    ) {
        return $this->defaultTransformFactory()
            ->create($result)
            ->transform($value);
    }

    private $validator;
    private $defaultTransformFactory;
}
