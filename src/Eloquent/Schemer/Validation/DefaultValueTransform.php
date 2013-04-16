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

use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Value;
use stdClass;

class DefaultValueTransform extends Value\Visitor\AbstractContextualValueVisitor implements
    Value\Transform\ValueTransformInterface
{
    /**
     * @param Result\ValidationResult $result
     * @param Comparator|null         $comparator
     */
    public function __construct(
        Result\ValidationResult $result,
        Comparator $comparator = null
    ) {
        parent::__construct();

        if (null === $comparator) {
            $comparator = new Comparator;
        }

        $this->result = $result;
        $this->comparator = $comparator;
    }

    /**
     * @return Result\ValidationResult
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * @return Comparator
     */
    public function comparator()
    {
        return $this->comparator;
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return Value\ValueInterface
     */
    public function transform(Value\ValueInterface $value)
    {
        $this->clear();
        $value = $value->accept($this);
        $this->clear();

        return $value;
    }

    /**
     * @param Value\ArrayValue $value
     *
     * @return Value\ArrayValue
     */
    public function visitArrayValue(Value\ArrayValue $value)
    {
        $subValues = array();
        foreach ($value as $index => $subValue) {
            $this->pushContextAtom(strval($index));
            $subValues[$index] = $subValue->accept($this);
            $this->popContextAtom();
        }
        $subValues = array_merge($subValues, $this->defaultItems());

        return new Value\ArrayValue($subValues);
    }

    /**
     * @param Value\BooleanValue $value
     *
     * @return Value\BooleanValue
     */
    public function visitBooleanValue(Value\BooleanValue $value)
    {
        return $value;
    }

    /**
     * @param Value\FloatingPointValue $value
     *
     * @return Value\FloatingPointValue
     */
    public function visitFloatingPointValue(Value\FloatingPointValue $value)
    {
        return $value;
    }

    /**
     * @param Value\IntegerValue $value
     *
     * @return Value\IntegerValue
     */
    public function visitIntegerValue(Value\IntegerValue $value)
    {
        return $value;
    }

    /**
     * @param Value\NullValue $value
     *
     * @return Value\NullValue
     */
    public function visitNullValue(Value\NullValue $value)
    {
        return $value;
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return Value\ObjectValue
     */
    public function visitObjectValue(Value\ObjectValue $value)
    {
        $subValues = new stdClass;
        foreach ($value as $property => $subValue) {
            $this->pushContextAtom($property);
            $subValues->$property = $subValue->accept($this);
            $this->popContextAtom();
        }
        foreach ($this->defaultProperties() as $property => $subValue) {
            $subValues->$property = $subValue;
        }

        return new Value\ObjectValue($subValues);
    }

    /**
     * @param Value\StringValue $value
     *
     * @return Value\StringValue
     */
    public function visitStringValue(Value\StringValue $value)
    {
        return $value;
    }

    /**
     * @param Value\DateTimeValue $value
     *
     * @return Value\DateTimeValue
     */
    public function visitDateTimeValue(Value\DateTimeValue $value)
    {
        return $value;
    }

    /**
     * @param Value\ReferenceValue $value
     *
     * @return Value\ReferenceValue
     */
    public function visitReferenceValue(Value\ReferenceValue $value)
    {
        return $value;
    }

    /**
     * @return array<string,Value\ValueInterface>
     */
    protected function defaultProperties()
    {
        $defaultProperties = array();
        foreach ($this->result()->defaultValueMatches() as $match) {
            if (
                $this->comparator()->equals(
                    $this->context(),
                    $match->pointer()->parent()
                )
            ) {
                $atoms = $match->pointer()->atoms();
                $defaultProperties[array_pop($atoms)] = $match
                    ->schema()
                    ->defaultValue();
            }
        }

        return $defaultProperties;
    }

    /**
     * @return array<integer,Value\ValueInterface>
     */
    protected function defaultItems()
    {
        $defaultItems = array();
        foreach ($this->defaultProperties() as $index => $value) {
            $defaultItems[intval($index)] = $value;
        }

        return $defaultItems;
    }

    private $result;
    private $comparator;
}
