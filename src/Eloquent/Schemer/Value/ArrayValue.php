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

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

class ArrayValue extends AbstractValue implements Countable, IteratorAggregate
{
    /**
     * @param array<integer,mixed> $value
     */
    public function __construct(array $value)
    {
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
     * @return mixed
     */
    public function rawValue()
    {
        return array_map(function ($value) {
            return $value->rawValue();
        }, $this->value());
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->value());
    }

    /**
     * @param integer $index
     *
     * @return boolean
     */
    public function has($index)
    {
        return array_key_exists($index, $this->value());
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
        $value = $this->value();

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
        $value = $this->value();

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
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->value());
    }
}
