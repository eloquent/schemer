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
use Eloquent\Schemer\Constraint\StringValue\EmailFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\HostnameFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Ipv4AddressFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\Ipv6AddressFormatConstraint;
use Eloquent\Schemer\Constraint\StringValue\MaximumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\MinimumLengthConstraint;
use Eloquent\Schemer\Constraint\StringValue\PatternConstraint;
use Eloquent\Schemer\Constraint\StringValue\UriFormatConstraint;
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
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;
use Zend\Validator\Ip;
use Zend\Validator\Uri;
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
            $uriValidator = new Uri;
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
     * @param ConstraintInterface   $constraint
     * @param ValueInterface        $value
     * @param PointerInterface|null $entryPoint
     *
     * @return Result\ValidationResult
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
        $subResult = new Result\ValidationResult;
        foreach ($schema->constraints() as $constraint) {
            $subResult = $subResult->merge($constraint->accept($this));
        }

        if (!$subResult->isValid()) {
            return $subResult;
        }

        $result = new Result\ValidationResult(
            null,
            array($this->createMatch($schema))
        );
        $result = $result->merge($subResult);

        return $result;
    }

    // generic constraints =====================================================

    /**
     * @param EnumConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitEnumConstraint(EnumConstraint $constraint)
    {
        $value = $this->currentValue();
        foreach ($constraint->values() as $enumValue) {
            if ($this->comparator()->equals($value, $enumValue)) {
                return new Result\ValidationResult;
            }
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param TypeConstraint $constraint
     *
     * @return Result\ValidationResult
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
                return new Result\ValidationResult;
            }
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param AllOfConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitAllOfConstraint(AllOfConstraint $constraint)
    {
        if (1 === count($constraint->schemas())) {
            $schemas = $constraint->schemas();

            return $schemas[0]->accept($this);
        }

        $result = new Result\ValidationResult;
        foreach ($constraint->schemas() as $schema) {
            $result = $result->merge($schema->accept($this));
        }

        return $result;
    }

    /**
     * @param AnyOfConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitAnyOfConstraint(AnyOfConstraint $constraint)
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

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param OneOfConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitOneOfConstraint(OneOfConstraint $constraint)
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

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param NotConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitNotConstraint(NotConstraint $constraint)
    {
        if (!$constraint->schema()->accept($this)->isValid()) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    // object constraints ======================================================

    /**
     * @param MaximumPropertiesConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumPropertiesConstraint(MaximumPropertiesConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            $value->count() <= $constraint->maximum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param MinimumPropertiesConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumPropertiesConstraint(MinimumPropertiesConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            $value->count() >= $constraint->minimum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param RequiredConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitRequiredConstraint(RequiredConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            $value->has($constraint->property())
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param PropertiesConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitPropertiesConstraint(PropertiesConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof ObjectValue) {
            return new Result\ValidationResult;
        }

        $result = new Result\ValidationResult;
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
                    new Result\ValidationResult(
                        array(),
                        array($this->createDefaultValueMatch($schema))
                    )
                );
            }
        }

        // pattern properties
        foreach ($constraint->patternSchemas() as $pattern => $schema) {
            $pattern = $this->wrapPattern($pattern);

            foreach ($value->properties() as $property) {
                if (preg_match($pattern, $property)) {
                    $matchedProperties[$property] = true;
                    $result = $result->merge(
                        $this->validateObjectProperty($property, $schema)
                    );
                }
            }
        }

        // additional properties
        foreach ($value->properties() as $property) {
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
     * @param AdditionalPropertyConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitAdditionalPropertyConstraint(AdditionalPropertyConstraint $constraint)
    {
        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param DependencyConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitDependencyConstraint(DependencyConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ObjectValue ||
            !$value->has($constraint->property())
        ) {
            return new Result\ValidationResult;
        }

        return $constraint->schema()->accept($this);
    }

    // array constraints =======================================================

    /**
     * @param ItemsConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitItemsConstraint(ItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof ArrayValue) {
            return new Result\ValidationResult;
        }

        $result = new Result\ValidationResult;
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
                    new Result\ValidationResult(
                        array(),
                        array($this->createDefaultValueMatch($schema))
                    )
                );
            }
        }

        // additional items
        foreach ($value->indices() as $index) {
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
     * @param AdditionalItemConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitAdditionalItemConstraint(AdditionalItemConstraint $constraint)
    {
        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param MaximumItemsConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumItemsConstraint(MaximumItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ArrayValue ||
            $value->count() <= $constraint->maximum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param MinimumItemsConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumItemsConstraint(MinimumItemsConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof ArrayValue ||
            $value->count() >= $constraint->minimum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param UniqueItemsConstraint $constraint
     *
     * @return Result\ValidationResult
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
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    // string constraints ======================================================

    /**
     * @param MaximumLengthConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumLengthConstraint(MaximumLengthConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            mb_strlen($value->value(), 'UTF-8') <= $constraint->maximum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param MinimumLengthConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumLengthConstraint(MinimumLengthConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            mb_strlen($value->value(), 'UTF-8') >= $constraint->minimum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param PatternConstraint $constraint
     *
     * @return Result\ValidationResult
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
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param DateTimeFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitDateTimeFormatConstraint(DateTimeFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return new Result\ValidationResult;
        }

        $value = $this->currentValue();
        if (!$value instanceof StringValue) {
            return new Result\ValidationResult;
        }

        $formats = array(
            DateTime::ISO8601,
            'Y-m-d\TH:i:s\Z',
            'Y-m-d\TH:i:sP',
        );
        foreach ($formats as $format) {
            if (false !== DateTime::createFromFormat($format, $value->value())) {
                return new Result\ValidationResult;
            }
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param EmailFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitEmailFormatConstraint(EmailFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return new Result\ValidationResult;
        }

        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            $this->emailValidator()->isValid($value->value())
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param HostnameFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitHostnameFormatConstraint(HostnameFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return new Result\ValidationResult;
        }

        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            $this->hostnameValidator()->isValid($value->value())
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param Ipv4AddressFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitIpv4AddressFormatConstraint(Ipv4AddressFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return new Result\ValidationResult;
        }

        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            $this->ipv4AddressValidator()->isValid($value->value())
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param Ipv6AddressFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitIpv6AddressFormatConstraint(Ipv6AddressFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return new Result\ValidationResult;
        }

        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            $this->ipv6AddressValidator()->isValid($value->value())
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param UriFormatConstraint $constraint
     *
     * @return mixed
     */
    public function visitUriFormatConstraint(UriFormatConstraint $constraint)
    {
        if (!$this->formatValidationEnabled()) {
            return new Result\ValidationResult;
        }

        $value = $this->currentValue();
        if (
            !$value instanceof StringValue ||
            $this->uriValidator()->isValid($value->value())
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    // number constraints ======================================================

    /**
     * @param MultipleOfConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMultipleOfConstraint(MultipleOfConstraint $constraint)
    {
        $value = $this->currentValue();
        if (!$value instanceof NumberValueInterface) {
            return new Result\ValidationResult;
        }

        if (
            $value instanceof FloatingPointValue ||
            is_float($constraint->quantity())
        ) {
            if (0 == fmod($value->value(), $constraint->quantity())) {
                return new Result\ValidationResult;
            }
        } else {
            if (0 === $value->value() % $constraint->quantity()) {
                return new Result\ValidationResult;
            }
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param MaximumConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumConstraint(MaximumConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof NumberValueInterface ||
            $value->value() <= $constraint->maximum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param MinimumConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumConstraint(MinimumConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof NumberValueInterface ||
            $value->value() >= $constraint->minimum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    // date-time constraints ===================================================

    /**
     * @param MaximumDateTimeConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMaximumDateTimeConstraint(MaximumDateTimeConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof DateTimeValue ||
            $value->value() <= $constraint->maximum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
    }

    /**
     * @param MinimumDateTimeConstraint $constraint
     *
     * @return Result\ValidationResult
     */
    public function visitMinimumDateTimeConstraint(MinimumDateTimeConstraint $constraint)
    {
        $value = $this->currentValue();
        if (
            !$value instanceof DateTimeValue ||
            $value->value() >= $constraint->minimum()
        ) {
            return new Result\ValidationResult;
        }

        return new Result\ValidationResult(
            array($this->createIssue($constraint))
        );
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
            $pointer->joinAtom($property)
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
            $pointer->joinAtom(strval($index))
        ));
        $result = $schema->accept($this);
        $this->popContext();

        return $result;
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
     * @param Schema $schema
     *
     * @return Result\ValidationMatch
     */
    protected function createDefaultValueMatch(Schema $schema)
    {
        list(, $pointer) = $this->currentContext();

        return new Result\DefaultValueMatch(
            $schema,
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

    private $formatValidationEnabled;
    private $comparator;
    private $emailValidator;
    private $hostnameValidator;
    private $ipv4AddressValidator;
    private $ipv6AddressValidator;
    private $uriValidator;
    private $contextStack;
}
