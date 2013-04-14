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

use DateTime;
use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Constraint\ArrayValue\AdditionalItemConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\ItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MaximumItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MinimumItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\UniqueItemsConstraint;
use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Constraint\DateTimeValue\MaximumDateTimeConstraint;
use Eloquent\Schemer\Constraint\DateTimeValue\MinimumDateTimeConstraint;
use Eloquent\Schemer\Constraint\Generic\AllOfConstraint;
use Eloquent\Schemer\Constraint\Generic\AnyOfConstraint;
use Eloquent\Schemer\Constraint\Generic\EnumConstraint;
use Eloquent\Schemer\Constraint\Generic\NotConstraint;
use Eloquent\Schemer\Constraint\Generic\OneOfConstraint;
use Eloquent\Schemer\Constraint\Generic\TypeConstraint;
use Eloquent\Schemer\Constraint\NumberValue\MaximumConstraint;
use Eloquent\Schemer\Constraint\NumberValue\MinimumConstraint;
use Eloquent\Schemer\Constraint\NumberValue\MultipleOfConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\AdditionalPropertyConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\DependencyConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MaximumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MinimumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\RequiredConstraint;
use Eloquent\Schemer\Constraint\StringValue\DateTimeFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\MaximumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\MinimumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\PatternConstraint;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Pointer\Pointer;
use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\BooleanValue;
use Eloquent\Schemer\Value\DateTimeValue;
use Eloquent\Schemer\Value\FloatingPointValue;
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
     * @param Comparator|null $comparator
     */
    public function __construct(Comparator $comparator = null)
    {
        if (null === $comparator) {
            $comparator = new Comparator;
        }

        $this->comparator = $comparator;
    }

    /**
     * @return Comparator
     */
    public function comparator()
    {
        return $this->comparator;
    }

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
     * @param EnumConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitEnumConstraint(EnumConstraint $constraint)
    {
        $value = $this->currentValue();
        foreach ($constraint->values() as $enumValue) {
            if ($this->comparator()->equals($value, $enumValue)) {
                return array();
            }
        }

        return array($this->createIssue($constraint));
    }

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

        if (1 === $matchingSchemas) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param NotConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitNotConstraint(NotConstraint $constraint)
    {
        if (count($constraint->schema()->accept($this)) > 0) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    // object constraints ======================================================

    /**
     * @param MaximumPropertiesConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMaximumPropertiesConstraint(MaximumPropertiesConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            $value->count() <= $constraint->maximum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param MinimumPropertiesConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMinimumPropertiesConstraint(MinimumPropertiesConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            $value->count() >= $constraint->minimum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param RequiredConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitRequiredConstraint(RequiredConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            $value->has($constraint->property())
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param PropertiesConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitPropertiesConstraint(PropertiesConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof ObjectValue) {
            return array();
        }

        $issues = array();
        $matchedProperties = array();

        // properties
        foreach ($constraint->schemas() as $property => $schema) {
            if ($value->has($property)) {
                $matchedProperties[$property] = true;
                $issues = array_merge(
                    $issues,
                    $this->validateObjectProperty($property, $schema)
                );
            }
        }

        // pattern properties
        foreach ($constraint->patternSchemas() as $pattern => $schema) {
            $pattern = $this->wrapPattern($pattern);

            foreach ($value->properties() as $property) {
                if (preg_match($pattern, $property)) {
                    $matchedProperties[$property] = true;
                    $issues = array_merge(
                        $issues,
                        $this->validateObjectProperty($property, $schema)
                    );
                }
            }
        }

        // additional properties
        foreach ($value->properties() as $property) {
            if (!array_key_exists($property, $matchedProperties)) {
                $issues = array_merge(
                    $issues,
                    $this->validateObjectProperty(
                        $property,
                        $constraint->additionalSchema()
                    )
                );
            }
        }

        return $issues;
    }

    /**
     * @param AdditionalPropertyConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitAdditionalPropertyConstraint(AdditionalPropertyConstraint $constraint)
    {
        return array($this->createIssue($constraint));
    }

    /**
     * @param DependencyConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitDependencyConstraint(DependencyConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            !$value->has($constraint->property())
        ) {
            return array();
        }

        return $constraint->schema()->accept($this);
    }

    // array constraints =======================================================

    /**
     * @param ItemsConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitItemsConstraint(ItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof ArrayValue) {
            return array();
        }

        $issues = array();
        $matchedIndices = array();

        // items
        foreach ($constraint->schemas() as $index => $schema) {
            if ($value->has($index)) {
                $matchedIndices[$index] = true;
                $issues = array_merge(
                    $issues,
                    $this->validateArrayIndex($index, $schema)
                );
            }
        }

        // additional items
        foreach ($value->indices() as $index) {
            if (!array_key_exists($index, $matchedIndices)) {
                $issues = array_merge(
                    $issues,
                    $this->validateArrayIndex(
                        $index,
                        $constraint->additionalSchema()
                    )
                );
            }
        }

        return $issues;
    }

    /**
     * @param AdditionalItemConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitAdditionalItemConstraint(AdditionalItemConstraint $constraint)
    {
        return array($this->createIssue($constraint));
    }

    /**
     * @param MaximumItemsConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMaximumItemsConstraint(MaximumItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ArrayValue ||
            $value->count() <= $constraint->maximum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param MinimumItemsConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMinimumItemsConstraint(MinimumItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ArrayValue ||
            $value->count() >= $constraint->minimum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param UniqueItemsConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitUniqueItemsConstraint(UniqueItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ArrayValue ||
            !$constraint->value() ||
            $this->comparator->equals(
                $value->value(),
                $this->unique($value->value())
            )
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    // string constraints ======================================================

    /**
     * @param MaximumLengthConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMaximumLengthConstraint(MaximumLengthConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            mb_strlen($value->value(), 'UTF-8') <= $constraint->maximum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param MinimumLengthConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMinimumLengthConstraint(MinimumLengthConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            mb_strlen($value->value(), 'UTF-8') >= $constraint->minimum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param PatternConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitPatternConstraint(PatternConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            preg_match(
                $this->wrapPattern($constraint->pattern()),
                $value->value()
            )
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param DateTimeFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitDateTimeFormatConstraint(DateTimeFormatConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof StringValue) {
            return array();
        }

        $formats = array(
            DateTime::ISO8601,
            'Y-m-d\TH:i:s\Z',
            'Y-m-d\TH:i:sP',
        );
        foreach ($formats as $format) {
            if (false !== DateTime::createFromFormat($format, $value->value())) {
                return array();
            }
        }

        return array($this->createIssue($constraint));
    }

    // number constraints ======================================================

    /**
     * @param MultipleOfConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMultipleOfConstraint(MultipleOfConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof NumberValueInterface) {
            return array();
        }

        if (
            $value instanceof FloatingPointValue ||
            is_float($constraint->quantity())
        ) {
            if (0 == fmod($value->value(), $constraint->quantity())) {
                return array();
            }
        } else {
            if (0 === $value->value() % $constraint->quantity()) {
                return array();
            }
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param MaximumConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMaximumConstraint(MaximumConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof NumberValueInterface ||
            $value->value() <= $constraint->maximum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param MinimumConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMinimumConstraint(MinimumConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof NumberValueInterface ||
            $value->value() >= $constraint->minimum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    // date-time constraints ===================================================

    /**
     * @param MaximumDateTimeConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMaximumDateTimeConstraint(MaximumDateTimeConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof DateTimeValue ||
            $value->value() <= $constraint->maximum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    /**
     * @param MinimumDateTimeConstraint $constraint
     *
     * @return array<Result\ValidationIssue>
     */
    public function visitMinimumDateTimeConstraint(MinimumDateTimeConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof DateTimeValue ||
            $value->value() >= $constraint->minimum()
        ) {
            return array();
        }

        return array($this->createIssue($constraint));
    }

    // implementation details ==================================================

    /**
     * @param string $property
     * @param Schema $schema
     *
     * @return array<Result\ValidationIssue>
     */
    protected function validateObjectProperty($property, Schema $schema)
    {
        list($value, $pointer) = $this->currentContext();
        $this->pushContext(array(
            $value->get($property),
            $pointer->joinAtom($property)
        ));
        $issues = $schema->accept($this);
        $this->popContext();

        return $issues;
    }

    /**
     * @param integer $index
     * @param Schema  $schema
     *
     * @return array<Result\ValidationIssue>
     */
    protected function validateArrayIndex($index, Schema $schema)
    {
        list($value, $pointer) = $this->currentContext();
        $this->pushContext(array(
            $value->get($index),
            $pointer->joinAtom(strval($index))
        ));
        $issues = $schema->accept($this);
        $this->popContext();

        return $issues;
    }

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

    /**
     * @param array<integer,mixed> $array
     *
     * @return array<integer,mixed>
     */
    protected function unique(array $array)
    {
        $unique = array();
        foreach ($array as $value) {
            if (!in_array($value, $unique, true)) {
                $unique[] = $value;
            }
        }

        return $unique;
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    protected function wrapPattern($pattern)
    {
        return sprintf('/%s/', str_replace('/', '\\/', $pattern));
    }

    private $comparator;
    private $contextStack;
}
