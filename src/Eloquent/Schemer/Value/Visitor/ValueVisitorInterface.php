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

interface ValueVisitorInterface
{
    /**
     * @param Value\ArrayValue $value
     *
     * @return mixed
     */
    public function visitArrayValue(Value\ArrayValue $value);

    /**
     * @param Value\BooleanValue $value
     *
     * @return mixed
     */
    public function visitBooleanValue(Value\BooleanValue $value);

    /**
     * @param Value\FloatingPointValue $value
     *
     * @return mixed
     */
    public function visitFloatingPointValue(Value\FloatingPointValue $value);

    /**
     * @param Value\IntegerValue $value
     *
     * @return mixed
     */
    public function visitIntegerValue(Value\IntegerValue $value);

    /**
     * @param Value\NullValue $value
     *
     * @return mixed
     */
    public function visitNullValue(Value\NullValue $value);

    /**
     * @param Value\ObjectValue $value
     *
     * @return mixed
     */
    public function visitObjectValue(Value\ObjectValue $value);

    /**
     * @param Value\StringValue $value
     *
     * @return mixed
     */
    public function visitStringValue(Value\StringValue $value);

    /**
     * @param Value\DateTimeValue $value
     *
     * @return mixed
     */
    public function visitDateTimeValue(Value\DateTimeValue $value);

    /**
     * @param Value\ReferenceValue $value
     *
     * @return mixed
     */
    public function visitReferenceValue(Value\ReferenceValue $value);

    /**
     * @param Value\PlaceholderValue $value
     *
     * @return mixed
     */
    public function visitPlaceholderValue(Value\PlaceholderValue $value);
}
