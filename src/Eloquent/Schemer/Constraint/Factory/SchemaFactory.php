<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Factory;

use DateTime;
use Eloquent\Schemer\Constraint\ArrayValue;
use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\DateTimeValue;
use Eloquent\Schemer\Constraint\Generic;
use Eloquent\Schemer\Constraint\NumberValue;
use Eloquent\Schemer\Constraint\ObjectValue;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\StringValue;
use Eloquent\Schemer\Validation\ConstraintValidator;
use Eloquent\Schemer\Validation\ConstraintValidatorInterface;
use Eloquent\Schemer\Value;
use RuntimeException;

class SchemaFactory implements SchemaFactoryInterface
{
    /**
     * @param FormatConstraintFactoryInterface|null $formatConstraintFactory
     * @param Schema|null                           $metaSchema
     * @param ConstraintValidatorInterface|null     $constraintValidator
     */
    public function __construct(
        FormatConstraintFactoryInterface $formatConstraintFactory = null,
        Schema $metaSchema = null,
        ConstraintValidatorInterface $constraintValidator = null
    ) {
        if (null === $formatConstraintFactory) {
            $formatConstraintFactory = new FormatConstraintFactory;
        }
        if (null === $constraintValidator && null !== $metaSchema) {
            $constraintValidator = new ConstraintValidator;
        }

        $this->formatConstraintFactory = $formatConstraintFactory;
        $this->metaSchema = $metaSchema;
        $this->constraintValidator = $constraintValidator;
    }

    /**
     * @return FormatConstraintFactoryInterface
     */
    public function formatConstraintFactory()
    {
        return $this->formatConstraintFactory;
    }

    /**
     * @return Schema|null
     */
    public function metaSchema()
    {
        return $this->metaSchema;
    }

    /**
     * @return ConstraintValidatorInterface|null
     */
    public function constraintValidator()
    {
        return $this->constraintValidator;
    }

    /**
     * @param Value\ConcreteValueInterface $value
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function create(Value\ConcreteValueInterface $value)
    {
        if (null !== $this->metaSchema()) {
            $result = $this->constraintValidator()->validate(
                $this->metaSchema(),
                $value
            );
            if (!$result->isValid()) {
                throw new RuntimeException('Invalid schema.');
            }
        }

        $this->clear();
        $schema = $this->createSchema($value);
        $this->clear();

        return $schema;
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    protected function createSchema(Value\ObjectValue $value)
    {
        if ($this->hasRegisteredSchema($value)) {
            return $this->registeredSchema($value);
        }

        if ($value->has('default')) {
            $defaultValue = $value->get('default');
        } else {
            $defaultValue = null;
        }

        $schema = new Schema(
            null,
            $defaultValue,
            $value->getRawDefault('title'),
            $value->getRawDefault('description')
        );
        $this->registerSchema($value, $schema);

        $constraints = array();
        foreach ($value as $property => $subValue) {
            $constraints = array_merge(
                $constraints,
                $this->createConstraints($property, $subValue)
            );
        }
        $constraints = array_merge(
            $constraints,
            $this->createCompositeConstraints($value)
        );
        $schema->setConstraints($constraints);

        return $schema;
    }

    /**
     * @param string                       $property
     * @param Value\ConcreteValueInterface $value
     *
     * @return array<ConstraintInterface>
     */
    protected function createConstraints(
        $property,
        Value\ConcreteValueInterface $value
    ) {
        switch ($property) {
            // generic constraints
            case 'enum':
                return array($this->createEnumConstraint($value));
            case 'type':
                return array($this->createTypeConstraint($value));
            case 'allOf':
                return array($this->createAllOfConstraint($value));
            case 'anyOf':
                return array($this->createAnyOfConstraint($value));
            case 'oneOf':
                return array($this->createOneOfConstraint($value));
            case 'not':
                return array($this->createNotConstraint($value));

            // object constraints
            case 'maxProperties':
                return array($this->createMaximumPropertiesConstraint($value));
            case 'minProperties':
                return array($this->createMinimumPropertiesConstraint($value));
            case 'required':
                return $this->createRequiredConstraints($value);
            case 'dependencies':
                return $this->createDependencyConstraints($value);

            // array constraints
            case 'maxItems':
                return array($this->createMaximumItemsConstraint($value));
            case 'minItems':
                return array($this->createMinimumItemsConstraint($value));
            case 'uniqueItems':
                return array($this->createUniqueItemsConstraint($value));

            // string constraints
            case 'maxLength':
                return array($this->createMaximumLengthConstraint($value));
            case 'minLength':
                return array($this->createMinimumLengthConstraint($value));
            case 'pattern':
                return array($this->createPatternConstraint($value));

            // number constraints
            case 'multipleOf':
                return array($this->createMultipleOfConstraint($value));

            // date-time constraints
            case 'maxDateTime':
                return array($this->createMaximumDateTimeConstraint($value));
            case 'minDateTime':
                return array($this->createMinimumDateTimeConstraint($value));

            // format constraints
            case 'format':
                $constraint = $this->formatConstraintFactory()->create($value->value());
                if (null === $constraint) {
                    return array();
                }

                return array($constraint);
        }

        return array();
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return array<ConstraintInterface>
     */
    protected function createCompositeConstraints(Value\ObjectValue $value)
    {
        $constraints = array();

        if ($constraint = $this->createPropertiesConstraint($value)) {
            $constraints[] = $constraint;
        }
        if ($constraint = $this->createItemsConstraint($value)) {
            $constraints[] = $constraint;
        }
        if ($constraint = $this->createMaximumConstraint($value)) {
            $constraints[] = $constraint;
        }
        if ($constraint = $this->createMinimumConstraint($value)) {
            $constraints[] = $constraint;
        }

        return $constraints;
    }

    // generic constraints =====================================================

    /**
     * @param Value\ArrayValue $value
     *
     * @return Generic\EnumConstraint
     */
    protected function createEnumConstraint(Value\ArrayValue $value)
    {
        return new Generic\EnumConstraint($value);
    }

    /**
     * @param Value\ConcreteValueInterface $value
     *
     * @return Generic\TypeConstraint
     */
    protected function createTypeConstraint(Value\ConcreteValueInterface $value)
    {
        if ($value instanceof Value\ArrayValue) {
            $valueTypes = array();
            foreach ($value as $typeValue) {
                $valueTypes[] = Value\ValueType::instanceByValue($typeValue->value());
            }

            return new Generic\TypeConstraint($valueTypes);
        }

        return new Generic\TypeConstraint(
            array(Value\ValueType::instanceByValue($value->value()))
        );
    }

    /**
     * @param Value\ArrayValue $value
     *
     * @return Generic\AllOfConstraint
     */
    protected function createAllOfConstraint(Value\ArrayValue $value)
    {
        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->createSchema($subValue);
        }

        return new Generic\AllOfConstraint($schemas);
    }

    /**
     * @param Value\ArrayValue $value
     *
     * @return Generic\AnyOfConstraint
     */
    protected function createAnyOfConstraint(Value\ArrayValue $value)
    {
        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->createSchema($subValue);
        }

        return new Generic\AnyOfConstraint($schemas);
    }

    /**
     * @param Value\ArrayValue $value
     *
     * @return Generic\OneOfConstraint
     */
    protected function createOneOfConstraint(Value\ArrayValue $value)
    {
        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->createSchema($subValue);
        }

        return new Generic\OneOfConstraint($schemas);
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return Generic\NotConstraint
     */
    protected function createNotConstraint(Value\ObjectValue $value)
    {
        return new Generic\NotConstraint($this->createSchema($value));
    }

    // object constraints ======================================================

    /**
     * @param Value\IntegerValue $value
     *
     * @return ObjectValue\MaximumPropertiesConstraint
     */
    protected function createMaximumPropertiesConstraint(Value\IntegerValue $value)
    {
        return new ObjectValue\MaximumPropertiesConstraint($value->value());
    }

    /**
     * @param Value\IntegerValue $value
     *
     * @return ObjectValue\MinimumPropertiesConstraint
     */
    protected function createMinimumPropertiesConstraint(Value\IntegerValue $value)
    {
        return new ObjectValue\MinimumPropertiesConstraint($value->value());
    }

    /**
     * @param Value\ArrayValue $value
     *
     * @return array<RequiredConstraint>
     */
    protected function createRequiredConstraints(Value\ArrayValue $value)
    {
        $constraints = array();
        foreach ($value as $subValue) {
            $constraints[] = $this->createRequiredConstraint($subValue);
        }

        return $constraints;
    }

    /**
     * @param Value\StringValue $value
     *
     * @return array<RequiredConstraint>
     */
    protected function createRequiredConstraint(Value\StringValue $value)
    {
        return new ObjectValue\RequiredConstraint($value->value());
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return ObjectValue\PropertiesConstraint|null
     */
    protected function createPropertiesConstraint(Value\ObjectValue $value)
    {
        if (
            !$value->has('properties') &&
            !$value->has('patternProperties') &&
            !$value->has('additionalProperties')
        ) {
            return null;
        }

        $schemas = array();
        if ($value->has('properties')) {
            foreach ($value->get('properties') as $property => $subValue) {
                $schemas[$property] = $this->createSchema($subValue);
            }
        }

        $patternSchemas = array();
        if ($value->has('patternProperties')) {
            foreach ($value->get('patternProperties') as $pattern => $subValue) {
                $patternSchemas[$pattern] = $this->createSchema($subValue);
            }
        }

        if ($value->has('additionalProperties')) {
            if ($value->get('additionalProperties') instanceof Value\BooleanValue) {
                if ($value->getRaw('additionalProperties')) {
                    $additionalSchema = new Schema;
                } else {
                    $additionalSchema = new Schema(
                        array(new ObjectValue\AdditionalPropertyConstraint)
                    );
                }
            } else {
                $additionalSchema = $this->createSchema($value->get('additionalProperties'));
            }
        } else {
            $additionalSchema = new Schema;
        }

        return new ObjectValue\PropertiesConstraint(
            $schemas,
            $patternSchemas,
            $additionalSchema
        );
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return array<DependencyConstraint>
     */
    protected function createDependencyConstraints(Value\ObjectValue $value)
    {
        $constraints = array();
        foreach ($value as $property => $subValue) {
            if ($subValue instanceof Value\ArrayValue) {
                $subConstraints = array();
                foreach ($subValue as $subSubValue) {
                    $subConstraints[] = $this->createRequiredConstraint($subSubValue);
                }

                $constraints[] = new ObjectValue\DependencyConstraint(
                    $property,
                    new Schema($subConstraints)
                );
            } else {
                $constraints[] = new ObjectValue\DependencyConstraint(
                    $property,
                    $this->createSchema($subValue)
                );
            }
        }

        return $constraints;
    }

    // array constraints =======================================================

    /**
     * @param Value\ObjectValue $value
     *
     * @return ArrayValue\ItemsConstraint|null
     */
    protected function createItemsConstraint(Value\ObjectValue $value)
    {
        if (
            !$value->has('items') &&
            !$value->has('additionalItems')
        ) {
            return null;
        }

        $schemas = array();
        $additionalSchema = null;
        if ($value->has('items')) {
            if ($value->get('items') instanceof Value\ArrayValue) {
                foreach ($value->get('items') as $subValue) {
                    $schemas[] = $this->createSchema($subValue);
                }
            } else {
                $additionalSchema = $this->createSchema($value->get('items'));
            }
        }

        if (null === $additionalSchema && $value->has('additionalItems')) {
            if ($value->get('additionalItems') instanceof Value\BooleanValue) {
                if (!$value->getRaw('additionalItems')) {
                    $additionalSchema = new Schema(
                        array(new ArrayValue\AdditionalItemConstraint)
                    );
                }
            } else {
                $additionalSchema = $this->createSchema($value->get('additionalItems'));
            }
        }

        if (null === $additionalSchema) {
            $additionalSchema = new Schema;
        }

        return new ArrayValue\ItemsConstraint($schemas, $additionalSchema);
    }

    /**
     * @param Value\IntegerValue $value
     *
     * @return ArrayValue\MaximumItemsConstraint
     */
    protected function createMaximumItemsConstraint(Value\IntegerValue $value)
    {
        return new ArrayValue\MaximumItemsConstraint($value->value());
    }

    /**
     * @param Value\IntegerValue $value
     *
     * @return ArrayValue\MinimumItemsConstraint
     */
    protected function createMinimumItemsConstraint(Value\IntegerValue $value)
    {
        return new ArrayValue\MinimumItemsConstraint($value->value());
    }

    /**
     * @param Value\BooleanValue $value
     *
     * @return ArrayValue\UniqueItemsConstraint
     */
    protected function createUniqueItemsConstraint(Value\BooleanValue $value)
    {
        return new ArrayValue\UniqueItemsConstraint($value->value());
    }

    // string constraints ======================================================

    /**
     * @param Value\IntegerValue $value
     *
     * @return StringValue\MaximumLengthConstraint
     */
    protected function createMaximumLengthConstraint(Value\IntegerValue $value)
    {
        return new StringValue\MaximumLengthConstraint($value->value());
    }

    /**
     * @param Value\IntegerValue $value
     *
     * @return StringValue\MinimumLengthConstraint
     */
    protected function createMinimumLengthConstraint(Value\IntegerValue $value)
    {
        return new StringValue\MinimumLengthConstraint($value->value());
    }

    /**
     * @param Value\StringValue $value
     *
     * @return StringValue\PatternConstraint
     */
    protected function createPatternConstraint(Value\StringValue $value)
    {
        return new StringValue\PatternConstraint($value->value());
    }

    // number constraints ======================================================

    /**
     * @param Value\NumberValueInterface $value
     *
     * @return NumberValue\MultipleOfConstraint
     */
    protected function createMultipleOfConstraint(Value\NumberValueInterface $value)
    {
        return new NumberValue\MultipleOfConstraint($value->value());
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return NumberValue\MaximumConstraint|null
     */
    protected function createMaximumConstraint(Value\ObjectValue $value)
    {
        if (!$value->has('maximum')) {
            return null;
        }

        return new NumberValue\MaximumConstraint(
            $value->getRaw('maximum'),
            $value->getRawDefault('exclusiveMaximum')
        );
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return NumberValue\MinimumConstraint|null
     */
    protected function createMinimumConstraint(Value\ObjectValue $value)
    {
        if (!$value->has('minimum')) {
            return null;
        }

        return new NumberValue\MinimumConstraint(
            $value->getRaw('minimum'),
            $value->getRawDefault('exclusiveMinimum')
        );
    }

    // date-time constraints ===================================================

    /**
     * @param Value\ConcreteValueInterface $value
     *
     * @return DateTimeValue\MaximumDateTimeConstraint
     */
    protected function createMaximumDateTimeConstraint(Value\ConcreteValueInterface $value)
    {
        if ($value instanceof Value\DateTimeValue) {
            return new DateTimeValue\MaximumDateTimeConstraint($value->value());
        }

        return new DateTimeValue\MaximumDateTimeConstraint(
            new DateTime($value->value())
        );
    }

    /**
     * @param Value\ConcreteValueInterface $value
     *
     * @return DateTimeValue\MinimumDateTimeConstraint
     */
    protected function createMinimumDateTimeConstraint(Value\ConcreteValueInterface $value)
    {
        if ($value instanceof Value\DateTimeValue) {
            return new DateTimeValue\MinimumDateTimeConstraint($value->value());
        }

        return new DateTimeValue\MinimumDateTimeConstraint(
            new DateTime($value->value())
        );
    }

    // implementation details ==================================================

    protected function clear()
    {
        $this->schemas = array();
    }

    /**
     * @param Value\ConcreteValueInterface $value
     * @param Schema                       $schema
     */
    protected function registerSchema(
        Value\ConcreteValueInterface $value,
        Schema $schema
    ) {
        $this->schemas[spl_object_hash($value)] = $schema;
    }

    /**
     * @param Value\ConcreteValueInterface $value
     *
     * @return boolean
     */
    protected function hasRegisteredSchema(Value\ConcreteValueInterface $value)
    {
        return array_key_exists(spl_object_hash($value), $this->schemas);
    }

    /**
     * @param Value\ConcreteValueInterface $value
     *
     * @return Value\ConcreteValueInterface
     */
    protected function registeredSchema(Value\ConcreteValueInterface $value)
    {
        if (!$this->hasRegisteredSchema($value)) {
            throw new LogicException('Undefined schema.');
        }

        return $this->schemas[spl_object_hash($value)];
    }

    private $formatConstraintFactory;
    private $metaSchema;
    private $constraintValidator;
    private $schemas;
}
