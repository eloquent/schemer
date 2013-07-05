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
use Eloquent\Schemer\Constraint\ArrayValue;
use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\DateTimeValue;
use Eloquent\Schemer\Constraint\Generic;
use Eloquent\Schemer\Constraint\NumberValue;
use Eloquent\Schemer\Constraint\ObjectValue;
use Eloquent\Schemer\Constraint\PlaceholderSchema;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\StringValue;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;
use Eloquent\Schemer\Pointer\Pointer;
use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Value;
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;
use Zend\Validator\Ip;
use Zend\Validator\Uri as UriValidator;
use Zend\Validator\ValidatorInterface;

class ConstraintValidator implements
    ConstraintValidatorInterface,
    ConstraintVisitorInterface
{
    /**
     * @param boolean|null            $formatValidationEnabled
     * @param Comparator|null         $comparator
     * @param ValidatorInterface|null $emailValidator
     * @param ValidatorInterface|null $hostnameValidator
     * @param ValidatorInterface|null $ipv4AddressValidator
     * @param ValidatorInterface|null $ipv6AddressValidator
     * @param ValidatorInterface|null $uriValidator
     */
    public function __construct(
        $formatValidationEnabled = null,
        Comparator $comparator = null,
        ValidatorInterface $emailValidator = null,
        ValidatorInterface $hostnameValidator = null,
        ValidatorInterface $ipv4AddressValidator = null,
        ValidatorInterface $ipv6AddressValidator = null,
        ValidatorInterface $uriValidator = null
    ) {
        if (null === $formatValidationEnabled) {
            $formatValidationEnabled = true;
        }
        if (null === $comparator) {
            $comparator = new Comparator;
        }
        if (null === $emailValidator) {
            $emailValidator = new EmailAddress;
        }
        if (null === $hostnameValidator) {
            $hostnameValidator = new Hostname;
        }
        if (null === $ipv4AddressValidator) {
            $ipv4AddressValidator = new Ip(array(
                'allowipv4' => true,
                'allowipv6' => false,
                'allowipvfuture' => false,
                'allowliteral' => false,
            ));
        }
        if (null === $ipv6AddressValidator) {
            $ipv6AddressValidator = new Ip(array(
                'allowipv4' => false,
                'allowipv6' => true,
                'allowipvfuture' => false,
                'allowliteral' => false,
            ));
        }
        if (null === $uriValidator) {
            $uriValidator = new UriValidator('Eloquent\Schemer\Uri\Uri');
        }

        $this->formatValidationEnabled = $formatValidationEnabled;
        $this->comparator = $comparator;
        $this->emailValidator = $emailValidator;
        $this->hostnameValidator = $hostnameValidator;
        $this->ipv4AddressValidator = $ipv4AddressValidator;
        $this->ipv6AddressValidator = $ipv6AddressValidator;
        $this->uriValidator = $uriValidator;
    }

    /**
     * @param boolean $formatValidationEnabled
     */
    public function setFormatValidationEnabled($formatValidationEnabled)
    {
        $this->formatValidationEnabled = $formatValidationEnabled;
    }

    /**
     * @return boolean
     */
    public function formatValidationEnabled()
    {
        return $this->formatValidationEnabled;
    }

    /**
     * @return Comparator
     */
    public function comparator()
    {
        return $this->comparator;
    }

    /**
     * @return ValidatorInterface
     */
    public function emailValidator()
    {
        return $this->emailValidator;
    }

    /**
     * @return ValidatorInterface
     */
    public function hostnameValidator()
    {
        return $this->hostnameValidator;
    }

    /**
     * @return ValidatorInterface
     */
    public function ipv4AddressValidator()
    {
        return $this->ipv4AddressValidator;
    }

    /**
     * @return ValidatorInterface
     */
    public function ipv6AddressValidator()
    {
        return $this->ipv6AddressValidator;
    }

    /**
     * @return ValidatorInterface
     */
    public function uriValidator()
    {
        return $this->uriValidator;
    }

    /**
     * @param ConstraintInterface          $constraint
     * @param Value\ConcreteValueInterface &$value
     * @param PointerInterface|null        $entryPoint
     *
     * @return Result\ValidationResult
     */
    public function validate(
        ConstraintInterface $constraint,
        Value\ConcreteValueInterface &$value,
        PointerInterface $entryPoint = null
    ) {
        if (null === $entryPoint) {
            $entryPoint = new Pointer;
        }

        $this->clear();

        $this->pushContext(array($value, $entryPoint));
        $result = $constraint->accept($this);

        $this->clear();

        return $result;
    }

    /**
     * @param Schema $schema
     *
     * @return Result\ValidationResult
     */
    public function visitSchema(Schema $schema)
    {
        $result = $this->result($schema);
        if (null !== $result) {
            return $result;
        }

        $result = $this->register($schema, $this->createResult());

        foreach ($schema->constraints() as $constraint) {
            $result = $result->merge($constraint->accept($this));
        }

        if ($result->isValid()) {
            $result->addMatch($this->createMatch($schema));
        }

        return $result;
    }

    /**
     * @param PlaceholderSchema $schema
     *
     * @return Result\ValidationResult
     */
    public function visitPlaceholderSchema(PlaceholderSchema $schema)
    {
        return $schema->innerSchema()->accept($this);
    }

    // generic constraints =====================================================

    /**
     * @param Generic\EnumConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitEnumConstraint(Generic\EnumConstraint $constraint)
    {
        $value = $this->currentValue();
        foreach ($constraint->values() as $enumValue) {
            if ($this->comparator()->equals($value, $enumValue)) {
                return $this->createResult();
            }
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param Generic\TypeConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitTypeConstraint(Generic\TypeConstraint $constraint)
    {
        $value = $this->currentValue();
        $isValid = false;
        foreach ($constraint->valueTypes() as $valueType) {
            if ($valueType === Value\ValueType::ARRAY_TYPE()) {
                $isValid = $value instanceof Value\ArrayValue;
            } elseif ($valueType === Value\ValueType::BOOLEAN_TYPE()) {
                $isValid = $value instanceof Value\BooleanValue;
            } elseif ($valueType === Value\ValueType::DATE_TIME_TYPE()) {
                $isValid = $value instanceof Value\DateTimeValue;
            } elseif ($valueType === Value\ValueType::INTEGER_TYPE()) {
                $isValid = $value instanceof Value\IntegerValue;
            } elseif ($valueType === Value\ValueType::NULL_TYPE()) {
                $isValid = $value instanceof Value\NullValue;
            } elseif ($valueType === Value\ValueType::NUMBER_TYPE()) {
                $isValid = $value instanceof Value\NumberValueInterface;
            } elseif ($valueType === Value\ValueType::OBJECT_TYPE()) {
                $isValid = $value instanceof Value\ObjectValue;
            } elseif ($valueType === Value\ValueType::STRING_TYPE()) {
                $isValid = $value instanceof Value\StringValue;
            }

            if ($isValid) {
                return $this->createResult();
            }
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param Generic\AllOfConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitAllOfConstraint(Generic\AllOfConstraint $constraint)
    {
        if (1 === count($constraint->schemas())) {
            $schemas = $constraint->schemas();

            return $schemas[0]->accept($this);
        }

        $result = $this->createResult();
        foreach ($constraint->schemas() as $schema) {
            $result = $result->merge($schema->accept($this));
        }

        return $result;
    }

    /**
     * @param Generic\AnyOfConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitAnyOfConstraint(Generic\AnyOfConstraint $constraint)
    {
        if (1 === count($constraint->schemas())) {
            $schemas = $constraint->schemas();

            return $schemas[0]->accept($this);
        }

        foreach ($constraint->schemas() as $schema) {
            $result = $schema->accept($this);
            if ($result->isValid()) {
                return $result;
            }
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param Generic\OneOfConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitOneOfConstraint(Generic\OneOfConstraint $constraint)
    {
        if (1 === count($constraint->schemas())) {
            $schemas = $constraint->schemas();

            return $schemas[0]->accept($this);
        }

        $validResults = array();
        foreach ($constraint->schemas() as $schema) {
            $result = $schema->accept($this);
            if ($result->isValid()) {
                $validResults[] = $result;
            }
        }

        if (1 === count($validResults)) {
            return array_shift($validResults);
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param Generic\NotConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitNotConstraint(Generic\NotConstraint $constraint)
    {
        if (!$constraint->schema()->accept($this)->isValid()) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    // object constraints ======================================================

    /**
     * @param ObjectValue\MaximumPropertiesConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumPropertiesConstraint(ObjectValue\MaximumPropertiesConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\ObjectValue ||
            $value->count() <= $constraint->maximum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param ObjectValue\MinimumPropertiesConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumPropertiesConstraint(ObjectValue\MinimumPropertiesConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\ObjectValue ||
            $value->count() >= $constraint->minimum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param ObjectValue\RequiredConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitRequiredConstraint(ObjectValue\RequiredConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\ObjectValue ||
            $value->has($constraint->property())
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param ObjectValue\PropertiesConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitPropertiesConstraint(ObjectValue\PropertiesConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof Value\ObjectValue) {
            return $this->createResult();
        }

        $result = $this->createResult();
        $matchedProperties = array();

        // properties
        foreach ($constraint->schemas() as $property => $schema) {
            if ($value->has($property)) {
                $matchedProperties[$property] = true;
                $result = $result->merge(
                    $this->validateObjectProperty($property, $schema)
                );
            } elseif (null !== $schema->defaultValue()) {
                $result = $result->merge(
                    $this->createResult(
                        array(),
                        array($this->createDefaultValueMatch($schema, $property))
                    )
                );
            }
        }

        // pattern properties
        foreach ($constraint->patternSchemas() as $pattern => $schema) {
            $pattern = $this->wrapPattern($pattern);

            foreach ($value->keys() as $property) {
                if (preg_match($pattern, $property)) {
                    $matchedProperties[$property] = true;
                    $result = $result->merge(
                        $this->validateObjectProperty($property, $schema)
                    );
                }
            }
        }

        // additional properties
        foreach ($value->keys() as $property) {
            if (!array_key_exists($property, $matchedProperties)) {
                $result = $result->merge(
                    $this->validateObjectProperty(
                        $property,
                        $constraint->additionalSchema()
                    )
                );
            }
        }

        return $result;
    }

    /**
     * @param ObjectValue\AdditionalPropertyConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitAdditionalPropertyConstraint(ObjectValue\AdditionalPropertyConstraint $constraint)
    {
        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param ObjectValue\DependencyConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitDependencyConstraint(ObjectValue\DependencyConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\ObjectValue ||
            !$value->has($constraint->property())
        ) {
            return $this->createResult();
        }

        return $constraint->schema()->accept($this);
    }

    // array constraints =======================================================

    /**
     * @param ArrayValue\ItemsConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitItemsConstraint(ArrayValue\ItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof Value\ArrayValue) {
            return $this->createResult();
        }

        $result = $this->createResult();
        $matchedIndices = array();

        // items
        foreach ($constraint->schemas() as $index => $schema) {
            if ($value->has($index)) {
                $matchedIndices[$index] = true;
                $result = $result->merge(
                    $this->validateArrayIndex($index, $schema)
                );
            } elseif (null !== $schema->defaultValue()) {
                $result = $result->merge(
                    $this->createResult(
                        array(),
                        array($this->createDefaultValueMatch($schema, $index))
                    )
                );
            }
        }

        // additional items
        foreach ($value->keys() as $index) {
            if (!array_key_exists($index, $matchedIndices)) {
                $result = $result->merge(
                    $this->validateArrayIndex(
                        $index,
                        $constraint->additionalSchema()
                    )
                );
            }
        }

        return $result;
    }

    /**
     * @param ArrayValue\AdditionalItemConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitAdditionalItemConstraint(ArrayValue\AdditionalItemConstraint $constraint)
    {
        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param ArrayValue\MaximumItemsConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumItemsConstraint(ArrayValue\MaximumItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\ArrayValue ||
            $value->count() <= $constraint->maximum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param ArrayValue\MinimumItemsConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumItemsConstraint(ArrayValue\MinimumItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\ArrayValue ||
            $value->count() >= $constraint->minimum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param ArrayValue\UniqueItemsConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitUniqueItemsConstraint(ArrayValue\UniqueItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\ArrayValue ||
            !$constraint->value() ||
            $this->comparator->equals(
                $value->value(),
                $this->unique($value->value())
            )
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    // string constraints ======================================================

    /**
     * @param StringValue\MaximumLengthConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumLengthConstraint(StringValue\MaximumLengthConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\StringValue ||
            mb_strlen($value->value(), 'UTF-8') <= $constraint->maximum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param StringValue\MinimumLengthConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumLengthConstraint(StringValue\MinimumLengthConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\StringValue ||
            mb_strlen($value->value(), 'UTF-8') >= $constraint->minimum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param StringValue\PatternConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitPatternConstraint(StringValue\PatternConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\StringValue ||
            preg_match(
                $this->wrapPattern($constraint->pattern()),
                $value->value()
            )
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param StringValue\DateTimeFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitDateTimeFormatConstraint(StringValue\DateTimeFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return $this->createResult();
        }

        $value = $this->currentValue();
        if (!$value instanceof Value\StringValue) {
            return $this->createResult();
        }

        $formats = array(
            DateTime::ISO8601,
            'Y-m-d\TH:i:s\Z',
            'Y-m-d\TH:i:sP',
        );
        foreach ($formats as $format) {
            if (false !== DateTime::createFromFormat($format, $value->value())) {
                return $this->createResult();
            }
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param StringValue\EmailFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitEmailFormatConstraint(StringValue\EmailFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return $this->createResult();
        }

        $value = $this->currentValue();
        if (
            !$value instanceof Value\StringValue ||
            $this->emailValidator()->isValid($value->value())
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param StringValue\HostnameFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitHostnameFormatConstraint(StringValue\HostnameFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return $this->createResult();
        }

        $value = $this->currentValue();
        if (
            !$value instanceof Value\StringValue ||
            $this->hostnameValidator()->isValid($value->value())
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param StringValue\Ipv4AddressFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitIpv4AddressFormatConstraint(StringValue\Ipv4AddressFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return $this->createResult();
        }

        $value = $this->currentValue();
        if (
            !$value instanceof Value\StringValue ||
            $this->ipv4AddressValidator()->isValid($value->value())
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param StringValue\Ipv6AddressFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitIpv6AddressFormatConstraint(StringValue\Ipv6AddressFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return $this->createResult();
        }

        $value = $this->currentValue();
        if (
            !$value instanceof Value\StringValue ||
            $this->ipv6AddressValidator()->isValid($value->value())
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param StringValue\UriFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitUriFormatConstraint(StringValue\UriFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return $this->createResult();
        }

        $value = $this->currentValue();
        if (
            !$value instanceof Value\StringValue ||
            $this->uriValidator()->isValid($value->value())
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    // number constraints ======================================================

    /**
     * @param NumberValue\MultipleOfConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMultipleOfConstraint(NumberValue\MultipleOfConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof Value\NumberValueInterface) {
            return $this->createResult();
        }

        if (
            $value instanceof Value\FloatingPointValue ||
            is_float($constraint->quantity())
        ) {
            if (0 == fmod($value->value(), $constraint->quantity())) {
                return $this->createResult();
            }
        } else {
            if (0 === $value->value() % $constraint->quantity()) {
                return $this->createResult();
            }
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param NumberValue\MaximumConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumConstraint(NumberValue\MaximumConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\NumberValueInterface ||
            $value->value() <= $constraint->maximum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param NumberValue\MinimumConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumConstraint(NumberValue\MinimumConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\NumberValueInterface ||
            $value->value() >= $constraint->minimum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    // date-time constraints ===================================================

    /**
     * @param DateTimeValue\MaximumDateTimeConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumDateTimeConstraint(DateTimeValue\MaximumDateTimeConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\DateTimeValue ||
            $value->value() <= $constraint->maximum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    /**
     * @param DateTimeValue\MinimumDateTimeConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumDateTimeConstraint(DateTimeValue\MinimumDateTimeConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof Value\DateTimeValue ||
            $value->value() >= $constraint->minimum()
        ) {
            return $this->createResult();
        }

        return $this->createSingleIssueResult($constraint);
    }

    // implementation details ==================================================

    /**
     * @param string $property
     * @param Schema $schema
     *
     * @return Result\ValidationResult
     */
    protected function validateObjectProperty($property, Schema $schema)
    {
        list($value, $pointer) = $this->currentContext();
        $this->pushContext(array(
            $value->get($property),
            $pointer->joinAtoms($property)
        ));
        $result = $schema->accept($this);
        $this->popContext();

        return $result;
    }

    /**
     * @param integer $index
     * @param Schema  $schema
     *
     * @return Result\ValidationResult
     */
    protected function validateArrayIndex($index, Schema $schema)
    {
        list($value, $pointer) = $this->currentContext();
        $this->pushContext(array(
            $value->get($index),
            $pointer->joinAtoms(strval($index))
        ));
        $result = $schema->accept($this);
        $this->popContext();

        return $result;
    }

    /**
     * @param tuple<ConcreteValueInterface,PointerInterface> $context
     */
    protected function pushContext(array $context)
    {
        array_push($this->contextStack, $context);
    }

    protected function popContext()
    {
        array_pop($this->contextStack);
    }

    protected function clear()
    {
        $this->contextStack = array();
        $this->results = array();
    }

    /**
     * @return tuple<ConcreteValueInterface,PointerInterface>
     */
    protected function currentContext()
    {
        return $this->contextStack[count($this->contextStack) - 1];
    }

    /**
     * @return ConcreteValueInterface
     */
    protected function currentValue()
    {
        list($value) = $this->currentContext();

        return $value;
    }

    /**
     * @return PointerInterface
     */
    protected function currentPointer()
    {
        list(, $pointer) = $this->currentContext();

        return $pointer;
    }

    /**
     * @param array<Result\ValidationIssue>|null $issues
     * @param array<Result\ValidationMatch>|null $matches
     *
     * @return Result\ValidationResult
     */
    protected function createResult(array $issues = null, array $matches = null)
    {
        return new Result\ValidationResult($issues, $matches);
    }

    /**
     * @param ConstraintInterface $constraint
     *
     * @return Result\ValidationResult
     */
    protected function createSingleIssueResult(ConstraintInterface $constraint)
    {
        return $this->createResult(array($this->createIssue($constraint)));
    }

    /**
     * @param ConstraintInterface $constraint
     *
     * @return Result\ValidationIssue
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
     * @param Schema $schema
     *
     * @return Result\ValidationMatch
     */
    protected function createMatch(Schema $schema)
    {
        list(, $pointer) = $this->currentContext();

        return new Result\ValidationMatch(
            $schema,
            $pointer
        );
    }

    /**
     * @param Schema         $schema
     * @param integer|string $atom
     *
     * @return Result\ValidationMatch
     */
    protected function createDefaultValueMatch(Schema $schema, $atom)
    {
        list(, $pointer) = $this->currentContext();

        return new Result\DefaultValueMatch(
            $schema,
            $pointer->joinAtoms($atom)
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

    /**
     * @param Schema                  $schema
     * @param Result\ValidationResult $result
     *
     * @return Result\ValidationResult
     */
    protected function register(
        Schema $schema,
        Result\ValidationResult $result
    ) {
        $this->results[$this->generateVisitKey($schema)] = $result;

        return $result;
    }

    /**
     * @param Schema $schema
     *
     * @return Result\ValidationResult|null
     */
    protected function result(Schema $schema)
    {
        $key = $this->generateVisitKey($schema);
        if (array_key_exists($key, $this->results)) {
            return $this->results[$key];
        }

        return null;
    }

    /**
     * @param Schema $schema
     *
     * @return string
     */
    protected function generateVisitKey(Schema $schema)
    {
        return sprintf(
            '%s.%s',
            spl_object_hash($schema),
            spl_object_hash($this->currentValue())
        );
    }

    private $formatValidationEnabled;
    private $comparator;
    private $emailValidator;
    private $hostnameValidator;
    private $ipv4AddressValidator;
    private $ipv6AddressValidator;
    private $uriValidator;

    private $contextStack;
    private $results;
}
