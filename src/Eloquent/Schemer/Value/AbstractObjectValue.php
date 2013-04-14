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
use stdClass;

abstract class AbstractObjectValue extends AbstractValue implements
    Countable,
    IteratorAggregate
{
    /**
     * @param stdClass $value
     */
    public function __construct(stdClass $value)
    {
        foreach (get_object_vars($value) as $property => $subValue) {
            if (!$subValue instanceof ValueInterface) {
                throw new InvalidArgumentException(
                    'Value must contain only instances of ValueInterface.'
                );
            }
        }

        parent::__construct($value);
    }

    /**
     * @return stdClass
     */
    public function value()
    {
        $value = clone $this->wrappedValue();
        foreach ($this->properties() as $property) {
            $value->$property = $value->$property->value();
        }

        return $value;
    }

    /**
     * @return ValueType
     */
    public function valueType()
    {
        return ValueType::OBJECT_TYPE();
    }

    /**
     * @return array<integer,string>
     */
    public function properties()
    {
        return array_keys(get_object_vars($this->wrappedValue()));
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count(get_object_vars($this->wrappedValue()));
    }

    /**
     * @param string $property
     *
     * @return boolean
     */
    public function has($property)
    {
        return property_exists($this->wrappedValue(), $property);
    }

    /**
     * @param string $property
     *
     * @return ValueInterface
     */
    public function get($property)
    {
        if (!$this->has($property)) {
            throw new Exception\UndefinedPropertyException($property);
        }

        return $this->wrappedValue()->$property;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function getRaw($property)
    {
        return $this->get($property)->value();
    }

    /**
     * @param string              $property
     * @param ValueInterface|null $default
     *
     * @return ValueInterface|null
     */
    public function getDefault($property, ValueInterface $default = null)
    {
        if (!$this->has($property)) {
            return $default;
        }

        return $this->wrappedValue()->$property;
    }

    /**
     * @param string $property
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getRawDefault($property, $default = null)
    {
        if (!$this->has($property)) {
            return $default;
        }

        return $this->getRaw($property);
    }

    /**
     * @param string $property
     *
     * @return ValueInterface
     */
    public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator(get_object_vars($this->wrappedValue()));
    }
}
