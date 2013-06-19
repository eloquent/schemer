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
use InvalidArgumentException;
use IteratorAggregate;
use stdClass;

class ObjectValue extends AbstractConcreteValue implements
    ValueContainerInterface,
    IteratorAggregate
{
    /**
     * @param stdClass|null $value
     */
    public function __construct(stdClass $value = null)
    {
        if (null === $value) {
            $value = new stdClass;
        }
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
     * @return ValueType
     */
    public function valueType()
    {
        return ValueType::OBJECT_TYPE();
    }

    /**
     * @return array<string,ValueInterface>
     */
    public function properties()
    {
        $properties = array();
        foreach (get_object_vars($this->value) as $property => $value) {
            if ('_empty_' === $property) {
                $properties[''] = $value;
            } else {
                $properties[$property] = $value;
            }
        }

        return $properties;
    }

    /**
     * @return array<integer,string>
     */
    public function keys()
    {
        return array_keys($this->properties());
    }

    /**
     * @return array<integer,ValueInterface>
     */
    public function values()
    {
        return array_values(get_object_vars($this->value));
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count(get_object_vars($this->value));
    }

    /**
     * @param integer        $property
     * @param ValueInterface $value
     */
    public function set($property, ValueInterface $value)
    {
        if ('' === $property) {
            $property = '_empty_';
        }

        $this->value->$property = $value;
    }

    /**
     * @param string $property
     *
     * @return boolean
     */
    public function has($property)
    {
        if ('' === $property) {
            $property = '_empty_';
        }

        return property_exists($this->value, $property);
    }

    /**
     * @param string $property
     *
     * @return ValueInterface
     */
    public function get($property)
    {
        if ('' === $property) {
            $property = '_empty_';
        }
        if (!$this->has($property)) {
            throw new Exception\UndefinedPropertyException($property);
        }

        return $this->value->$property;
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

        return $this->value->$property;
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
     * @param Visitor\ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(Visitor\ValueVisitorInterface $visitor)
    {
        return $visitor->visitObjectValue($this);
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
        return new ArrayIterator($this->properties());
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

        $valueMap[$id] = new stdClass;
        foreach (get_object_vars($this->value) as $property => $subValue) {
            $valueMap[$id]->$property = $subValue->unwrap($valueMap);
        }

        return $valueMap[$id];
    }
}
