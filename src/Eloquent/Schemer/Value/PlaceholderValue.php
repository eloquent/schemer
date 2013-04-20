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

class PlaceholderValue implements ValueInterface
{
    /**
     * @param ValueInterface|null $innerValue
     */
    public function setInnerValue(ValueInterface $innerValue = null)
    {
        $this->innerValue = $innerValue;
    }

    /**
     * @return ValueInterface|null
     */
    public function innerValue()
    {
        return $this->innerValue;
    }

    /**
     * @param ValueInterface $value
     *
     * @return boolean
     */
    public function refersTo(ValueInterface $value)
    {
        if ($this->innerValue() === $value) {
            return true;
        }
        if (
            $this->innerValue() instanceof self &&
            $this->innerValue()->innerValue() === $value
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isClosedRecursion()
    {
        return $this->refersTo($this);
    }

    /**
     * @return mixed
     */
    public function value()
    {
        if (null === $this->innerValue()) {
            return null;
        }

        return $this->innerValue()->value();
    }

    /**
     * @return ValueType
     */
    public function valueType()
    {
        if (null === $this->innerValue()) {
            return ValueType::NULL_VALUE();
        }

        return $this->innerValue()->valueType();
    }

    /**
     * @param Visitor\ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(Visitor\ValueVisitorInterface $visitor)
    {
        return $visitor->visitPlaceholderValue($this);
    }

    private $innerValue;
}
