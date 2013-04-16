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

use Eloquent\Schemer\Constraint\ArrayValue;
use Eloquent\Schemer\Constraint\DateTimeValue;
use Eloquent\Schemer\Constraint\Generic;
use Eloquent\Schemer\Constraint\NumberValue;
use Eloquent\Schemer\Constraint\ObjectValue;
use Eloquent\Schemer\Constraint\StringValue;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Value;

class SchemaFactory implements SchemaFactoryInterface
{
    /**
     * @param FormatConstraintFactoryInterface|null $formatConstraintFactory
     */
    public function __construct(
        FormatConstraintFactoryInterface $formatConstraintFactory = null
    ) {
        if (null === $formatConstraintFactory) {
            $formatConstraintFactory = new FormatConstraintFactory;
        }

        $this->formatConstraintFactory = $formatConstraintFactory;
    }

    /**
     * @return FormatConstraintFactoryInterface
     */
    public function formatConstraintFactory()
    {
        return $this->formatConstraintFactory;
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function create(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::OBJECT_TYPE())
            );
        }

        $constraints = array();
        foreach ($value as $property => $subValue) {
            $constraints = array_merge(
                $constraints,
                $this->createConstraints($property, $subValue)
            );
        }

        if ($value->has('default')) {
            $defaultValue = $value->get('default');
        } else {
            $defaultValue = null;
        }

        return new Schema(
            array_merge(
                $constraints,
                $this->createCompositeConstraints($value)
            ),
            $defaultValue,
            $value->getRawDefault('title'),
            $value->getRawDefault('description')
        );
    }

    /**
     * @param string               $property
     * @param Value\ValueInterface $value
     *
     * @return array<\Eloquent\Schemer\Constraint\ConstraintInterface>
     */
    protected function createConstraints(
        $property,
        Value\ValueInterface $value
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
     * @param Value\ValueInterface $value
     *
     * @return array<\Eloquent\Schemer\Constraint\ConstraintInterface>
     */
    protected function createCompositeConstraints(Value\ValueInterface $value)
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
     * @param Value\ValueInterface $value
     *
     * @return Generic\EnumConstraint
     */
    protected function createEnumConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::ARRAY_TYPE())
            );
        }

        return new Generic\EnumConstraint($value);
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return Generic\TypeConstraint
     */
    protected function createTypeConstraint(Value\ValueInterface $value)
    {
        if ($value instanceof Value\StringValue) {
            return new Generic\TypeConstraint(
                array(Value\ValueType::instanceByValue($value->value()))
            );
        } elseif ($value instanceof Value\ArrayValue) {
            $valueTypes = array();
            foreach ($value as $typeValue) {
                if (!$typeValue instanceof Value\StringValue) {
                    throw new UnexpectedValueException(
                        $typeValue->valueType(),
                        array(Value\ValueType::STRING_TYPE())
                    );
                }

                $valueTypes[] = Value\ValueType::instanceByValue($typeValue->value());
            }

            return new Generic\TypeConstraint($valueTypes);
        }

        throw new UnexpectedValueException(
            $value,
            array(Value\ValueType::STRING_TYPE(), Value\ValueType::ARRAY_TYPE())
        );
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return Generic\AllOfConstraint
     */
    protected function createAllOfConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::ARRAY_TYPE())
            );
        }

        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->create($subValue);
        }

        return new Generic\AllOfConstraint($schemas);
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return Generic\AnyOfConstraint
     */
    protected function createAnyOfConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::ARRAY_TYPE())
            );
        }

        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->create($subValue);
        }

        return new Generic\AnyOfConstraint($schemas);
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return Generic\OneOfConstraint
     */
    protected function createOneOfConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::ARRAY_TYPE())
            );
        }

        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->create($subValue);
        }

        return new Generic\OneOfConstraint($schemas);
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return Generic\NotConstraint
     */
    protected function createNotConstraint(Value\ValueInterface $value)
    {
        return new Generic\NotConstraint($this->create($value));
    }

    // object constraints ======================================================

    /**
     * @param Value\ValueInterface $value
     *
     * @return ObjectValue\MaximumPropertiesConstraint
     */
    protected function createMaximumPropertiesConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::INTEGER_TYPE())
            );
        }

        return new ObjectValue\MaximumPropertiesConstraint($value->value());
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return ObjectValue\MinimumPropertiesConstraint
     */
    protected function createMinimumPropertiesConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::INTEGER_TYPE())
            );
        }

        return new ObjectValue\MinimumPropertiesConstraint($value->value());
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return array<RequiredConstraint>
     */
    protected function createRequiredConstraints(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::ARRAY_TYPE())
            );
        }

        $constraints = array();
        foreach ($value as $subValue) {
            if (!$subValue instanceof Value\StringValue) {
                throw new UnexpectedValueException(
                    $subValue,
                    array(Value\ValueType::STRING_TYPE())
                );
            }

            $constraints[] = new ObjectValue\RequiredConstraint($subValue->value());
        }

        return $constraints;
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return ObjectValue\PropertiesConstraint|null
     */
    protected function createPropertiesConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::OBJECT_TYPE())
            );
        }

        if (
            !$value->has('properties') &&
            !$value->has('patternProperties') &&
            !$value->has('additionalProperties')
        ) {
            return null;
        }

        $schemas = array();
        if ($value->has('properties')) {
            if (!$value->get('properties') instanceof Value\ObjectValue) {
                throw new UnexpectedValueException(
                    $value->get('properties'),
                    array(Value\ValueType::OBJECT_TYPE())
                );
            }

            foreach ($value->get('properties') as $property => $subValue) {
                $schemas[$property] = $this->create($subValue);
            }
        }

        $patternSchemas = array();
        if ($value->has('patternProperties')) {
            if (!$value->get('patternProperties') instanceof Value\ObjectValue) {
                throw new UnexpectedValueException(
                    $value->get('patternProperties'),
                    array(Value\ValueType::OBJECT_TYPE())
                );
            }

            foreach ($value->get('patternProperties') as $pattern => $subValue) {
                $patternSchemas[$pattern] = $this->create($subValue);
            }
        }

        if ($value->has('additionalProperties')) {
            if ($value->get('additionalProperties') instanceof Value\ObjectValue) {
                $additionalSchema = $this->create($value->get('additionalProperties'));
            } elseif ($value->get('additionalProperties') instanceof Value\BooleanValue) {
                if ($value->getRaw('additionalProperties')) {
                    $additionalSchema = new Schema;
                } else {
                    $additionalSchema = new Schema(
                        array(new ObjectValue\AdditionalPropertyConstraint)
                    );
                }
            } else {
                throw new UnexpectedValueException(
                    $value->get('additionalProperties'),
                    array(Value\ValueType::OBJECT_TYPE(), Value\ValueType::BOOLEAN_TYPE())
                );
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
     * @param Value\ValueInterface $value
     *
     * @return array<DependencyConstraint>
     */
    protected function createDependencyConstraints(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::OBJECT_TYPE())
            );
        }

        $constraints = array();
        foreach ($value as $property => $subValue) {
            if ($subValue instanceof Value\ObjectValue) {
                $constraints[] = new ObjectValue\DependencyConstraint(
                    $property,
                    $this->create($subValue)
                );
            } elseif ($subValue instanceof Value\ArrayValue) {
                $subConstraints = array();
                foreach ($subValue as $subSubValue) {
                    if (!$subSubValue instanceof Value\StringValue) {
                        throw new UnexpectedValueException(
                            $subSubValue,
                            array(Value\ValueType::STRING_TYPE())
                        );
                    }

                    $subConstraints[] = new ObjectValue\RequiredConstraint(
                        $subSubValue->value()
                    );
                }

                $constraints[] = new ObjectValue\DependencyConstraint(
                    $property,
                    new Schema($subConstraints)
                );
            } else {
                throw new UnexpectedValueException(
                    $subValue,
                    array(Value\ValueType::OBJECT_TYPE(), Value\ValueType::ARRAY_TYPE())
                );
            }
        }

        return $constraints;
    }

    // array constraints =======================================================

    /**
     * @param Value\ValueInterface $value
     *
     * @return ArrayValue\ItemsConstraint|null
     */
    protected function createItemsConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::OBJECT_TYPE())
            );
        }

        if (
            !$value->has('items') &&
            !$value->has('additionalItems')
        ) {
            return null;
        }

        $schemas = array();
        $additionalSchema = null;
        if ($value->has('items')) {
            if ($value->get('items') instanceof Value\ObjectValue) {
                $additionalSchema = $this->create($value->get('items'));
            } elseif ($value->get('items') instanceof Value\ArrayValue) {
                foreach ($value->get('items') as $subValue) {
                    $schemas[] = $this->create($subValue);
                }
            } else {
                throw new UnexpectedValueException(
                    $value->get('items'),
                    array(Value\ValueType::OBJECT_TYPE(), Value\ValueType::ARRAY_TYPE())
                );
            }
        }

        if (null === $additionalSchema && $value->has('additionalItems')) {
            if ($value->get('additionalItems') instanceof Value\ObjectValue) {
                $additionalSchema = $this->create($value->get('additionalItems'));
            } elseif ($value->get('additionalItems') instanceof Value\BooleanValue) {
                if (!$value->getRaw('additionalItems')) {
                    $additionalSchema = new Schema(
                        array(new ArrayValue\AdditionalItemConstraint)
                    );
                }
            } else {
                throw new UnexpectedValueException(
                    $value->get('additionalItems'),
                    array(Value\ValueType::OBJECT_TYPE(), Value\ValueType::BOOLEAN_TYPE())
                );
            }
        }

        if (null === $additionalSchema) {
            $additionalSchema = new Schema;
        }

        return new ArrayValue\ItemsConstraint($schemas, $additionalSchema);
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return ArrayValue\MaximumItemsConstraint
     */
    protected function createMaximumItemsConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::INTEGER_TYPE())
            );
        }

        return new ArrayValue\MaximumItemsConstraint($value->value());
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return ArrayValue\MinimumItemsConstraint
     */
    protected function createMinimumItemsConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::INTEGER_TYPE())
            );
        }

        return new ArrayValue\MinimumItemsConstraint($value->value());
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return ArrayValue\UniqueItemsConstraint
     */
    protected function createUniqueItemsConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\BooleanValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::BOOLEAN_TYPE())
            );
        }

        return new ArrayValue\UniqueItemsConstraint($value->value());
    }

    // string constraints ======================================================

    /**
     * @param Value\ValueInterface $value
     *
     * @return StringValue\MaximumLengthConstraint
     */
    protected function createMaximumLengthConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::INTEGER_TYPE())
            );
        }

        return new StringValue\MaximumLengthConstraint($value->value());
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return StringValue\MinimumLengthConstraint
     */
    protected function createMinimumLengthConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::INTEGER_TYPE())
            );
        }

        return new StringValue\MinimumLengthConstraint($value->value());
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return StringValue\PatternConstraint
     */
    protected function createPatternConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\StringValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::STRING_TYPE())
            );
        }

        return new StringValue\PatternConstraint($value->value());
    }

    // number constraints ======================================================

    /**
     * @param Value\ValueInterface $value
     *
     * @return NumberValue\MultipleOfConstraint
     */
    protected function createMultipleOfConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\NumberValueInterface) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::NUMBER_TYPE())
            );
        }

        return new NumberValue\MultipleOfConstraint($value->value());
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return NumberValue\MaximumConstraint|null
     */
    protected function createMaximumConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::OBJECT_TYPE())
            );
        }

        if (!$value->has('maximum')) {
            return null;
        }

        return new NumberValue\MaximumConstraint(
            $value->getRaw('maximum'),
            $value->getRawDefault('exclusiveMaximum')
        );
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return NumberValue\MinimumConstraint|null
     */
    protected function createMinimumConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::OBJECT_TYPE())
            );
        }

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
     * @param Value\ValueInterface $value
     *
     * @return DateTimeValue\MaximumDateTimeConstraint
     */
    protected function createMaximumDateTimeConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\DateTimeValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::DATETIME_TYPE())
            );
        }

        return new DateTimeValue\MaximumDateTimeConstraint($value->value());
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return DateTimeValue\MinimumDateTimeConstraint
     */
    protected function createMinimumDateTimeConstraint(Value\ValueInterface $value)
    {
        if (!$value instanceof Value\DateTimeValue) {
            throw new UnexpectedValueException(
                $value,
                array(Value\ValueType::DATETIME_TYPE())
            );
        }

        return new DateTimeValue\MinimumDateTimeConstraint($value->value());
    }

    private $formatConstraintFactory;
}
