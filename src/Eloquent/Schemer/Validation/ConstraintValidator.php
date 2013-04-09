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
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Constraint\Generic\TypeConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertyConstraint;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Pointer\Pointer;
use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\BooleanValue;
use Eloquent\Schemer\Value\DateTimeValue;
use Eloquent\Schemer\Value\IntegerValue;
use Eloquent\Schemer\Value\NullValue;
use Eloquent\Schemer\Value\NumberValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\StringValue;
use Eloquent\Schemer\Value\ValueInterface;
use Eloquent\Schemer\Value\ValueType;
use LogicException;

class ConstraintValidator implements
    ConstraintValidatorInterface,
    ConstraintVisitorInterface
{
    /**
     * @param ConstraintInterface   $constraint
     * @param ValueInterface        $value
     * @param PointerInterface|null $entryPoint
     *
     * @return ValidationResult
     */
    public function validate(
        ConstraintInterface $constraint,
        ValueInterface $value,
        PointerInterface $entryPoint = null
    ) {
        if (null === $entryPoint) {
            $entryPoint = new Pointer;
        }

        $this->clear();

        $this->pushContext(array($value, $entryPoint));
        $constraint->accept($this);
        $result = new Result\ValidationResult($this->issues);

        $this->clear();

        return $result;
    }

    /**
     * @param Schema $constraint
     */
    public function visitSchema(Schema $constraint)
    {
        foreach ($constraint->constraints() as $subConstraint) {
            $subConstraint->accept($this);
        }
    }

    // generic constraints

    /**
     * @param TypeConstraint $constraint
     */
    public function visitTypeConstraint(TypeConstraint $constraint)
    {
        $value = $this->currentValue();
        $isValid = false;
        foreach ($constraint->types() as $type) {
            if ($type === ValueType::ARRAY_TYPE()) {
                $isValid = $value instanceof ArrayValue;
            } elseif ($type === ValueType::BOOLEAN_TYPE()) {
                $isValid = $value instanceof BooleanValue;
            } elseif ($type === ValueType::DATETIME_TYPE()) {
                $isValid = $value instanceof DateTimeValue;
            } elseif ($type === ValueType::INTEGER_TYPE()) {
                $isValid = $value instanceof IntegerValue;
            } elseif ($type === ValueType::NULL_TYPE()) {
                $isValid = $value instanceof NullValue;
            } elseif ($type === ValueType::NUMBER_TYPE()) {
                $isValid = $value instanceof NumberValue;
            } elseif ($type === ValueType::OBJECT_TYPE()) {
                $isValid = $value instanceof ObjectValue;
            } elseif ($type === ValueType::STRING_TYPE()) {
                $isValid = $value instanceof StringValue;
            }

            if ($isValid) {
                break;
            }
        }

        if (!$isValid) {
            $this->addIssue($constraint);
        }
    }

    // object constraints

    /**
     * @param PropertyConstraint $constraint
     */
    public function visitPropertyConstraint(PropertyConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            !$value->has($constraint->property())
        ) {
            return;
        }

        $this->pushContext(array(
            $value->get($constraint->property()),
            $this->currentPointer()->joinAtom($constraint->property())
        ));
        $constraint->schema()->accept($this);
        $this->popContext();
    }

    // implementation details

    /**
     * @param tuple<ValueInterface,PointerInterface> $context
     */
    protected function pushContext(array $context)
    {
        array_push($this->contextStack, $context);
    }

    /**
     * @throws LogicException
     */
    protected function popContext()
    {
        if (null === array_pop($this->contextStack)) {
            throw new LogicException('Validation context stack is empty.');
        }
    }

    protected function clear()
    {
        $this->contextStack = array();
        $this->issues = array();
    }

    /**
     * @return tuple<ValueInterface,PointerInterface>
     * @throws LogicException
     */
    protected function currentContext()
    {
        $count = count($this->contextStack);
        if ($count < 1) {
            throw new LogicException('Current validation context is undefined.');
        }

        return $this->contextStack[$count - 1];
    }

    /**
     * @return ValueInterface
     * @throws LogicException
     */
    protected function currentValue()
    {
        list($value) = $this->currentContext();

        return $value;
    }

    /**
     * @return PointerInterface
     * @throws LogicException
     */
    protected function currentPointer()
    {
        list(, $pointer) = $this->currentContext();

        return $pointer;
    }

    /**
     * @param ConstraintInterface $constraint
     */
    protected function addIssue(ConstraintInterface $constraint)
    {
        list($value, $pointer) = $this->currentContext();
        $this->issues[] = new Result\ValidationIssue(
            $constraint,
            $value,
            $pointer
        );
    }

    private $contextStack;
    private $issues;
}
