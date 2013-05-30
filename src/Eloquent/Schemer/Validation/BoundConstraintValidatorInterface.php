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

use Eloquent\Schemer\Value\ConcreteValueInterface;

interface BoundConstraintValidatorInterface
{
    /**
     * @return ConstraintInterface
     */
    public function constraint();

    /**
     * @param ConcreteValueInterface $value
     *
     * @return ValidationResult
     */
    public function validate(ConcreteValueInterface $value);
}
