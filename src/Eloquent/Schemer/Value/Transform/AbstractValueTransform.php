<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value\Transform;

use Eloquent\Schemer\Pointer\Pointer;
use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Value;
use stdClass;

abstract class AbstractValueTransform implements
    ValueTransformInterface,
    Value\Visitor\ValueVisitorInterface
{
    public function __construct()
    {
        $this->clear();
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

    protected function clear()
    {
        $this->setContext(new Pointer);
    }

    /**
     * @param PointerInterface $context
     */
    protected function setContext(PointerInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $atom
     */
    protected function pushContextAtom($atom)
    {
        $this->setContext($this->context()->joinAtom($atom));
    }

    protected function popContextAtom()
    {
        $this->setContext($this->context()->parent());
    }

    /**
     * @return PointerInterface
     */
    protected function context()
    {
        return $this->context;
    }

    private $context;
}
