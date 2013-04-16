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

use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\BooleanValue;
use Eloquent\Schemer\Value\DateTimeValue;
use Eloquent\Schemer\Value\FloatingPointValue;
use Eloquent\Schemer\Value\IntegerValue;
use Eloquent\Schemer\Value\NullValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\ReferenceValue;
use Eloquent\Schemer\Value\StringValue;

interface ValueVisitorInterface
{
    /**
     * @param ArrayValue $value
     *
     * @return mixed
     */
    public function visitArrayValue(ArrayValue $value);

    /**
     * @param BooleanValue $value
     *
     * @return mixed
     */
    public function visitBooleanValue(BooleanValue $value);

    /**
     * @param FloatingPointValue $value
     *
     * @return mixed
     */
    public function visitFloatingPointValue(FloatingPointValue $value);

    /**
     * @param IntegerValue $value
     *
     * @return mixed
     */
    public function visitIntegerValue(IntegerValue $value);

    /**
     * @param NullValue $value
     *
     * @return mixed
     */
    public function visitNullValue(NullValue $value);

    /**
     * @param ObjectValue $value
     *
     * @return mixed
     */
    public function visitObjectValue(ObjectValue $value);

    /**
     * @param StringValue $value
     *
     * @return mixed
     */
    public function visitStringValue(StringValue $value);

    /**
     * @param DateTimeValue $value
     *
     * @return mixed
     */
    public function visitDateTimeValue(DateTimeValue $value);

    /**
     * @param ReferenceValue $value
     *
     * @return mixed
     */
    public function visitReferenceValue(ReferenceValue $value);
}
