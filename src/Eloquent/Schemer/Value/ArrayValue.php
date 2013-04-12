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

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use LogicException;

class ArrayValue extends AbstractValue implements
    ArrayAccess,
    Countable,
    IteratorAggregate
{
    /**
     * @param array<integer,mixed>|null $value
     */
    public function __construct(array $value = null)
    {
        if (null === $value) {
            $value = array();
        }

        $expectedIndex = 0;
        foreach ($value as $index => $subValue) {
            if ($index !== $expectedIndex++) {
                throw new InvalidArgumentException(
                    'Value must be a sequential array.'
                );
            }
            if (!$subValue instanceof ValueInterface) {
                throw new InvalidArgumentException(
                    'Value must contain only instances of ValueInterface.'
                );
            }
        }

        parent::__construct($value);
    }

    /**
     * @return array<integer,mixed>
     */
    public function value()
    {
        return array_map(function ($value) {
            return $value->value();
        }, $this->wrappedValue());
    }

    /**
     * @return ValueType
     */
    public function valueType()
    {
        return ValueType::ARRAY_TYPE();
    }

    /**
     * @return array<integer,integer>
     */
    public function indices()
    {
        return array_keys($this->wrappedValue());
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->wrappedValue());
    }

    /**
     * @param integer $index
     *
     * @return boolean
     */
    public function has($index)
    {
        return array_key_exists($index, $this->wrappedValue());
    }

    /**
     * @param integer $index
     *
     * @return ValueInterface
     */
    public function get($index)
    {
        if (!$this->has($index)) {
            throw new Exception\UndefinedIndexException($index);
        }
        $value = $this->wrappedValue();

        return $value[$index];
    }

    /**
     * @param integer             $index
     * @param ValueInterface|null $default
     *
     * @return ValueInterface|null
     */
    public function getDefault($index, ValueInterface $default = null)
    {
        if (!$this->has($index)) {
            return $default;
        }
        $value = $this->wrappedValue();

        return $value[$index];
    }

    /**
     * @param ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ValueVisitorInterface $visitor)
    {
        return $visitor->visitArrayValue($this);
    }

    /**
     * @param integer $index
     *
     * @return boolean
     */
    public function offsetExists($index)
    {
        return $this->has($index);
    }

    /**
     * @param integer $index
     *
     * @return ValueInterface
     */
    public function offsetGet($index)
    {
        return $this->get($index);
    }

    /**
     * @param integer $index
     * @param mixed   $value
     *
     * @throws LogicException
     */
    public function offsetSet($index, $value)
    {
        throw new LogicException('Not supported.');
    }

    /**
     * @param integer $index
     *
     * @throws LogicException
     */
    public function offsetUnset($index)
    {
        throw new LogicException('Not supported.');
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->wrappedValue());
    }
}
