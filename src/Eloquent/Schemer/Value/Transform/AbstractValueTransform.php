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

abstract class AbstractValueTransform extends Value\Visitor\AbstractValueVisitor implements
    ValueTransformInterface
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
        $this->setValue($value);
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

    protected function clear()
    {
        $this->setContext(new Pointer);
        $this->setValue(null);
    }

    /**
     * @param Value\ValueInterface|null $value
     */
    protected function setValue(Value\ValueInterface $value = null)
    {
        $this->value = $value;
    }

    /**
     * @return ValueInterface|null
     */
    protected function value()
    {
        return $this->value;
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

    private $value;
    private $context;
}
