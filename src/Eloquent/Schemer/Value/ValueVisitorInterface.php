<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value;

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
     * @param IntegerValue $value
     *
     * @return mixed
     */
    public function visitIntegerValue(IntegerValue $value);

    /**
     * @param NumberValue $value
     *
     * @return mixed
     */
    public function visitNumberValue(NumberValue $value);

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
     * @param ReferenceValue $value
     *
     * @return mixed
     */
    public function visitReferenceValue(ReferenceValue $value);
}
