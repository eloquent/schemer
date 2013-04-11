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
use Eloquent\Schemer\Constraint\Generic\AllOfConstraint;
use Eloquent\Schemer\Constraint\Generic\AnyOfConstraint;
use Eloquent\Schemer\Constraint\Generic\NotConstraint;
use Eloquent\Schemer\Constraint\Generic\OneOfConstraint;
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
use Eloquent\Schemer\Value\NumberValueInterface;
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
        $result = new Result\ValidationResult($constraint->accept($this));

        $this->clear();

        return $result;
    }

    /**
     * @param Schema $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitSchema(Schema $constraint)
    {
        $issues = array();
        foreach ($constraint->constraints() as $subConstraint) {
            $issues = array_merge(
                $issues,
                $subConstraint->accept($this)
            );
        }

        return $issues;
    }

    // generic constraints =====================================================

    /**
     * @param TypeConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitTypeConstraint(TypeConstraint $constraint)
    {
        $value = $this->currentValue();
        $isValid = false;
        foreach ($constraint->valueTypes() as $valueType) {
            if ($valueType === ValueType::ARRAY_TYPE()) {
                $isValid = $value instanceof ArrayValue;
            } elseif ($valueType === ValueType::BOOLEAN_TYPE()) {
                $isValid = $value instanceof BooleanValue;
            } elseif ($valueType === ValueType::DATETIME_TYPE()) {
                $isValid = $value instanceof DateTimeValue;
            } elseif ($valueType === ValueType::INTEGER_TYPE()) {
                $isValid = $value instanceof IntegerValue;
            } elseif ($valueType === ValueType::NULL_TYPE()) {
                $isValid = $value instanceof NullValue;
            } elseif ($valueType === ValueType::NUMBER_TYPE()) {
                $isValid = $value instanceof NumberValueInterface;
            } elseif ($valueType === ValueType::OBJECT_TYPE()) {
                $isValid = $value instanceof ObjectValue;
            } elseif ($valueType === ValueType::STRING_TYPE()) {
                $isValid = $value instanceof StringValue;
            }

            if ($isValid) {
                return array();
            }
        }

        return array($this->createIssue($constraint));
    }

    // object constraints ======================================================

    /**
     * @param PropertyConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitPropertyConstraint(PropertyConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            !$value->has($constraint->property())
        ) {
            return array();
        }

        $this->pushContext(array(
            $value->get($constraint->property()),
            $this->currentPointer()->joinAtom($constraint->property())
        ));
        $issues = $constraint->schema()->accept($this);
        $this->popContext();

        return $issues;
    }

    /**
     * @param AllOfConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitAllOfConstraint(AllOfConstraint $constraint)
    {
        if (1 === count($constraint->schemas())) {
            $schemas = $constraint->schemas();

            return $schemas[0]->accept($this);
        }

        $issues = array();
        foreach ($constraint->schemas() as $schema) {
            $issues = array_merge($issues, $schema->accept($this));
        }

        return $issues;
    }

    /**
     * @param AnyOfConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitAnyOfConstraint(AnyOfConstraint $constraint)
    {
        if (1 === count($constraint->schemas())) {
            $schemas = $constraint->schemas();

            return $schemas[0]->accept($this);
        }

        foreach ($constraint->schemas() as $schema) {
            if (count($schema->accept($this)) < 1) {
                return array();
            }
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param OneOfConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitOneOfConstraint(OneOfConstraint $constraint)
    {
        if (1 === count($constraint->schemas())) {
            $schemas = $constraint->schemas();

            return $schemas[0]->accept($this);
        }

        $matchingSchemas = 0;
        foreach ($constraint->schemas() as $schema) {
            if (count($schema->accept($this)) < 1) {
                $matchingSchemas += 1;
            }
        }

        if (1 !== $matchingSchemas) {
            return array($this->createIssue($constraint));
        }

        return array();
    }

    /**
     * @param NotConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitNotConstraint(NotConstraint $constraint)
    {
        if (count($constraint->schema()->accept($this)) < 1) {
            return array($this->createIssue($constraint));
        }

        return array();
    }

    // implementation details ==================================================

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
    protected function createIssue(ConstraintInterface $constraint)
    {
        list($value, $pointer) = $this->currentContext();

        return new Result\ValidationIssue(
            $constraint,
            $value,
            $pointer
        );
    }

    private $contextStack;
}
