<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value\Visitor;

use Eloquent\Schemer\Value;

abstract class AbstractValueVisitor implements ValueVisitorInterface
{
    /**
     * @param Value\ArrayValue $value
     *
     * @return Value\ArrayValue
     */
    public function visitArrayValue(Value\ArrayValue $value)
    {
        return $value;
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
        return $value;
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
     * @param Value\PlaceholderValue $value
     *
     * @return Value\PlaceholderValue
     */
    public function visitPlaceholderValue(Value\PlaceholderValue $value)
    {
        return $value;
    }
}
