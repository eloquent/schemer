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

class DefaultValueTransform extends Value\Transform\AbstractValueTransform
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
     * @param Value\ArrayValue $value
     *
     * @return Value\ArrayValue
     */
    public function visitArrayValue(Value\ArrayValue $value)
    {
        return new Value\ArrayValue(
            array_merge(
                iterator_to_array(parent::visitArrayValue($value)),
                $this->defaultItems()
            )
        );
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return Value\ObjectValue
     */
    public function visitObjectValue(Value\ObjectValue $value)
    {
        $subValues = new stdClass;
        foreach (parent::visitObjectValue($value) as $property => $subValue) {
            $subValues->$property = $subValue;
        }
        foreach ($this->defaultProperties() as $property => $subValue) {
            $subValues->$property = $subValue;
        }

        return new Value\ObjectValue($subValues);
    }

    /**
     * @return array<string,Value\ConcreteValueInterface>
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
     * @return array<integer,Value\ConcreteValueInterface>
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
