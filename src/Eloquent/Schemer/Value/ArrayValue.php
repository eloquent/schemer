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
use InvalidArgumentException;
use IteratorAggregate;
use LogicException;

class ArrayValue extends AbstractConcreteValue implements
    ValueContainerInterface,
    ArrayAccess,
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
     * @return ValueType
     */
    public function valueType()
    {
        return ValueType::ARRAY_TYPE();
    }

    /**
     * @return array<integer,integer>
     */
    public function keys()
    {
        return array_keys($this->value);
    }

    /**
     * @return array<integer,ValueInterface>
     */
    public function values()
    {
        return array_values($this->value);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->value);
    }

    /**
     * @param integer        $index
     * @param ValueInterface $value
     */
    public function set($index, ValueInterface $value)
    {
        $this->value[$index] = $value;
    }

    /**
     * @param integer $index
     *
     * @return boolean
     */
    public function has($index)
    {
        return array_key_exists($index, $this->value);
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

        return $this->value[$index];
    }

    /**
     * @param integer $index
     *
     * @return mixed
     */
    public function getRaw($index)
    {
        return $this->get($index)->value();
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

        return $this->value[$index];
    }

    /**
     * @param integer $index
     * @param mixed   $default
     *
     * @return mixed
     */
    public function getRawDefault($index, $default = null)
    {
        if (!$this->has($index)) {
            return $default;
        }

        return $this->getRaw($index);
    }

    /**
     * @param Visitor\ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(Visitor\ValueVisitorInterface $visitor)
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
        return new ArrayIterator($this->value);
    }

    /**
     * @param array<tuple<string,mixed>> &$valueMap
     *
     * @return mixed
     */
    protected function unwrap(array &$valueMap)
    {
        $id = spl_object_hash($this);

        if (array_key_exists($id, $valueMap)) {
            return $valueMap[$id];
        }

        $valueMap[$id] = array();
        foreach ($this->value as $key => $subValue) {
            $valueMap[$id][$key] = $subValue->unwrap($valueMap);
        }

        return $valueMap[$id];
    }
}
