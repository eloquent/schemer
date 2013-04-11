<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference;

use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\BooleanValue;
use Eloquent\Schemer\Value\DateTimeValue;
use Eloquent\Schemer\Value\FloatingPointValue;
use Eloquent\Schemer\Value\IntegerValue;
use Eloquent\Schemer\Value\NullValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\StringValue;
use Eloquent\Schemer\Value\ValueInterface;
use Eloquent\Schemer\Value\ValueVisitorInterface;
use stdClass;

abstract class AbstractReferenceResolver implements ReferenceResolverInterface, ValueVisitorInterface
{
    /**
     * @param ValueInterface $value
     *
     * @return ValueInterface
     * @throws Exception\UndefinedReferenceException
     */
    public function resolve(ValueInterface $value)
    {
        return $value->accept($this);
    }

    /**
     * @param ArrayValue $value
     *
     * @return ArrayValue
     */
    public function visitArrayValue(ArrayValue $value)
    {
        $innerValue = array();
        foreach ($value as $index => $subValue) {
            $innerValue[$index] = $subValue->accept($this);
        }

        return new ArrayValue($innerValue);
    }

    /**
     * @param BooleanValue $value
     *
     * @return BooleanValue
     */
    public function visitBooleanValue(BooleanValue $value)
    {
        return $value;
    }

    /**
     * @param FloatingPointValue $value
     *
     * @return FloatingPointValue
     */
    public function visitFloatingPointValue(FloatingPointValue $value)
    {
        return $value;
    }

    /**
     * @param IntegerValue $value
     *
     * @return IntegerValue
     */
    public function visitIntegerValue(IntegerValue $value)
    {
        return $value;
    }

    /**
     * @param NullValue $value
     *
     * @return NullValue
     */
    public function visitNullValue(NullValue $value)
    {
        return $value;
    }

    /**
     * @param ObjectValue $value
     *
     * @return ObjectValue
     */
    public function visitObjectValue(ObjectValue $value)
    {
        $innerValue = new stdClass;
        foreach ($value as $property => $subValue) {
            $innerValue->$property = $subValue->accept($this);
        }

        return new ObjectValue($innerValue);
    }

    /**
     * @param StringValue $value
     *
     * @return StringValue
     */
    public function visitStringValue(StringValue $value)
    {
        return $value;
    }

    /**
     * @param DateTimeValue $value
     *
     * @return DateTimeValue
     */
    public function visitDateTimeValue(DateTimeValue $value)
    {
        return $value;
    }
}
