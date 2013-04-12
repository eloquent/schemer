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

use Eloquent\Schemer\Constraint\ArrayValue\AdditionalItemConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\ItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MaximumItemsConstraint;
use Eloquent\Schemer\Constraint\ArrayValue\MinimumItemsConstraint;
use Eloquent\Schemer\Constraint\Generic\AllOfConstraint;
use Eloquent\Schemer\Constraint\Generic\AnyOfConstraint;
use Eloquent\Schemer\Constraint\Generic\EnumConstraint;
use Eloquent\Schemer\Constraint\Generic\NotConstraint;
use Eloquent\Schemer\Constraint\Generic\OneOfConstraint;
use Eloquent\Schemer\Constraint\Generic\TypeConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\AdditionalPropertyConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\DependencyConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MaximumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\MinimumPropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\PropertiesConstraint;
use Eloquent\Schemer\Constraint\ObjectValue\RequiredConstraint;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\BooleanValue;
use Eloquent\Schemer\Value\IntegerValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\StringValue;
use Eloquent\Schemer\Value\ValueInterface;
use Eloquent\Schemer\Value\ValueType;

class SchemaFactory implements SchemaFactoryInterface
{
    /**
     * @param ValueInterface $value
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function create(ValueInterface $value)
    {
        if (!$value instanceof ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::OBJECT_TYPE())
            );
        }

        $constraints = array();
        foreach ($value as $property => $subValue) {
            $constraints = array_merge(
                $constraints,
                $this->createConstraints($property, $subValue)
            );
        }

        if ($constraint = $this->createPropertiesConstraint($value)) {
            $constraints[] = $constraint;
        }
        if ($constraint = $this->createItemsConstraint($value)) {
            $constraints[] = $constraint;
        }

        return new Schema($constraints);
    }

    /**
     * @param string         $property
     * @param ValueInterface $value
     *
     * @return array<\Eloquent\Schemer\Constraint\ConstraintInterface>
     */
    protected function createConstraints(
        $property,
        ValueInterface $value
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
        }

        return array();
    }

    // generic constraints =====================================================

    /**
     * @param ValueInterface $value
     *
     * @return EnumConstraint
     */
    protected function createEnumConstraint(ValueInterface $value)
    {
        if (!$value instanceof ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::ARRAY_TYPE())
            );
        }

        return new EnumConstraint($value);
    }

    /**
     * @param ValueInterface $value
     *
     * @return TypeConstraint
     */
    protected function createTypeConstraint(ValueInterface $value)
    {
        if ($value instanceof StringValue) {
            return new TypeConstraint(
                array(ValueType::instanceByValue($value->value()))
            );
        } elseif ($value instanceof ArrayValue) {
            $valueTypes = array();
            foreach ($value as $typeValue) {
                if (!$typeValue instanceof StringValue) {
                    throw new UnexpectedValueException(
                        $typeValue->valueType(),
                        array(ValueType::STRING_TYPE())
                    );
                }

                $valueTypes[] = ValueType::instanceByValue($typeValue->value());
            }

            return new TypeConstraint($valueTypes);
        }

        throw new UnexpectedValueException(
            $value,
            array(ValueType::STRING_TYPE(), ValueType::ARRAY_TYPE())
        );
    }

    /**
     * @param ValueInterface $value
     *
     * @return AllOfConstraint
     */
    protected function createAllOfConstraint(ValueInterface $value)
    {
        if (!$value instanceof ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::ARRAY_TYPE())
            );
        }

        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->create($subValue);
        }

        return new AllOfConstraint($schemas);
    }

    /**
     * @param ValueInterface $value
     *
     * @return AnyOfConstraint
     */
    protected function createAnyOfConstraint(ValueInterface $value)
    {
        if (!$value instanceof ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::ARRAY_TYPE())
            );
        }

        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->create($subValue);
        }

        return new AnyOfConstraint($schemas);
    }

    /**
     * @param ValueInterface $value
     *
     * @return OneOfConstraint
     */
    protected function createOneOfConstraint(ValueInterface $value)
    {
        if (!$value instanceof ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::ARRAY_TYPE())
            );
        }

        $schemas = array();
        foreach ($value as $subValue) {
            $schemas[] = $this->create($subValue);
        }

        return new OneOfConstraint($schemas);
    }

    /**
     * @param ValueInterface $value
     *
     * @return NotConstraint
     */
    protected function createNotConstraint(ValueInterface $value)
    {
        return new NotConstraint($this->create($value));
    }

    // object constraints ======================================================

    /**
     * @param ValueInterface $value
     *
     * @return MaximumPropertiesConstraint
     */
    protected function createMaximumPropertiesConstraint(ValueInterface $value)
    {
        if (!$value instanceof IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::INTEGER_TYPE())
            );
        }

        return new MaximumPropertiesConstraint($value->value());
    }

    /**
     * @param ValueInterface $value
     *
     * @return MinimumPropertiesConstraint
     */
    protected function createMinimumPropertiesConstraint(ValueInterface $value)
    {
        if (!$value instanceof IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::INTEGER_TYPE())
            );
        }

        return new MinimumPropertiesConstraint($value->value());
    }

    /**
     * @param ValueInterface $value
     *
     * @return array<RequiredConstraint>
     */
    protected function createRequiredConstraints(ValueInterface $value)
    {
        if (!$value instanceof ArrayValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::ARRAY_TYPE())
            );
        }

        $constraints = array();
        foreach ($value as $subValue) {
            if (!$subValue instanceof StringValue) {
                throw new UnexpectedValueException(
                    $subValue,
                    array(ValueType::STRING_TYPE())
                );
            }

            $constraints[] = new RequiredConstraint($subValue->value());
        }

        return $constraints;
    }

    /**
     * @param ValueInterface $value
     *
     * @return PropertiesConstraint|null
     */
    protected function createPropertiesConstraint(ValueInterface $value)
    {
        if (!$value instanceof ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::OBJECT_TYPE())
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
            if (!$value->get('properties') instanceof ObjectValue) {
                throw new UnexpectedValueException(
                    $value->get('properties'),
                    array(ValueType::OBJECT_TYPE())
                );
            }

            foreach ($value->get('properties') as $property => $subValue) {
                $schemas[$property] = $this->create($subValue);
            }
        }

        $patternSchemas = array();
        if ($value->has('patternProperties')) {
            if (!$value->get('patternProperties') instanceof ObjectValue) {
                throw new UnexpectedValueException(
                    $value->get('patternProperties'),
                    array(ValueType::OBJECT_TYPE())
                );
            }

            foreach ($value->get('patternProperties') as $pattern => $subValue) {
                $patternSchemas[$pattern] = $this->create($subValue);
            }
        }

        if ($value->has('additionalProperties')) {
            if ($value->get('additionalProperties') instanceof ObjectValue) {
                $additionalSchema = $this->create($value->get('additionalProperties'));
            } elseif ($value->get('additionalProperties') instanceof BooleanValue) {
                if ($value->get('additionalProperties')->value()) {
                    $additionalSchema = new Schema;
                } else {
                    $additionalSchema = new Schema(
                        array(new AdditionalPropertyConstraint)
                    );
                }
            } else {
                throw new UnexpectedValueException(
                    $value->get('additionalProperties'),
                    array(ValueType::OBJECT_TYPE(), ValueType::BOOLEAN_TYPE())
                );
            }
        } else {
            $additionalSchema = new Schema;
        }

        return new PropertiesConstraint(
            $schemas,
            $patternSchemas,
            $additionalSchema
        );
    }

    /**
     * @param ValueInterface $value
     *
     * @return array<DependencyConstraint>
     */
    protected function createDependencyConstraints(ValueInterface $value)
    {
        if (!$value instanceof ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::OBJECT_TYPE())
            );
        }

        $constraints = array();
        foreach ($value as $property => $subValue) {
            if ($subValue instanceof ObjectValue) {
                $constraints[] = new DependencyConstraint(
                    $property,
                    $this->create($subValue)
                );
            } elseif ($subValue instanceof ArrayValue) {
                $subConstraints = array();
                foreach ($subValue as $subSubValue) {
                    if (!$subSubValue instanceof StringValue) {
                        throw new UnexpectedValueException(
                            $subSubValue,
                            array(ValueType::STRING_TYPE())
                        );
                    }

                    $subConstraints[] = new RequiredConstraint(
                        $subSubValue->value()
                    );
                }

                $constraints[] = new DependencyConstraint(
                    $property,
                    new Schema($subConstraints)
                );
            } else {
                throw new UnexpectedValueException(
                    $subValue,
                    array(ValueType::OBJECT_TYPE(), ValueType::ARRAY_TYPE())
                );
            }
        }

        return $constraints;
    }

    // array constraints =======================================================

    /**
     * @param ValueInterface $value
     *
     * @return ItemsConstraint|null
     */
    protected function createItemsConstraint(ValueInterface $value)
    {
        if (!$value instanceof ObjectValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::OBJECT_TYPE())
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
            if ($value->get('items') instanceof ObjectValue) {
                $additionalSchema = $this->create($value->get('items'));
            } elseif ($value->get('items') instanceof ArrayValue) {
                foreach ($value->get('items') as $subValue) {
                    $schemas[] = $this->create($subValue);
                }
            } else {
                throw new UnexpectedValueException(
                    $value->get('items'),
                    array(ValueType::OBJECT_TYPE(), ValueType::ARRAY_TYPE())
                );
            }
        }

        if (null === $additionalSchema && $value->has('additionalItems')) {
            if ($value->get('additionalItems') instanceof ObjectValue) {
                $additionalSchema = $this->create($value->get('additionalItems'));
            } elseif ($value->get('additionalItems') instanceof BooleanValue) {
                if (!$value->get('additionalItems')->value()) {
                    $additionalSchema = new Schema(
                        array(new AdditionalItemConstraint)
                    );
                }
            } else {
                throw new UnexpectedValueException(
                    $value->get('additionalItems'),
                    array(ValueType::OBJECT_TYPE(), ValueType::BOOLEAN_TYPE())
                );
            }
        }

        if (null === $additionalSchema) {
            $additionalSchema = new Schema;
        }

        return new ItemsConstraint($schemas, $additionalSchema);
    }

    /**
     * @param ValueInterface $value
     *
     * @return MaximumItemsConstraint
     */
    protected function createMaximumItemsConstraint(ValueInterface $value)
    {
        if (!$value instanceof IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::INTEGER_TYPE())
            );
        }

        return new MaximumItemsConstraint($value->value());
    }

    /**
     * @param ValueInterface $value
     *
     * @return MinimumItemsConstraint
     */
    protected function createMinimumItemsConstraint(ValueInterface $value)
    {
        if (!$value instanceof IntegerValue) {
            throw new UnexpectedValueException(
                $value,
                array(ValueType::INTEGER_TYPE())
            );
        }

        return new MinimumItemsConstraint($value->value());
    }
}
