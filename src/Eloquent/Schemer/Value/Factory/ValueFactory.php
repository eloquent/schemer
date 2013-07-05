<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value\Factory;

use DateTime;
use Eloquent\Schemer\Uri\Uri;
use Eloquent\Schemer\Value;
use stdClass;

class ValueFactory implements ValueFactoryInterface
{
    /**
     * @param mixed &$value
     *
     * @return Value\ValueInterface
     */
    public function create(&$value)
    {
        $this->clear();
        $instance = $this->createValue($value);
        $this->clear();

        return $instance;
    }

    /**
     * @param mixed &$value
     *
     * @return Value\ValueInterface
     */
    protected function createValue(&$value)
    {
        $instance = $this->instance($value);
        if (null !== $instance) {
            return $instance;
        }

        $variableType = gettype($value);
        switch ($variableType) {
            case 'boolean':
                return new Value\BooleanValue($value);
            case 'integer':
                return new Value\IntegerValue($value);
            case 'double':
                return new Value\FloatingPointValue($value);
            case 'NULL':
                return new Value\NullValue;
            case 'string':
                return new Value\StringValue($value);
            case 'array':
                return $this->createArray($value);
            case 'object':
                if ($value instanceof DateTime) {
                    return new Value\DateTimeValue($value);
                }

                return $this->createObject($value);
        }

        throw new Exception\UnsupportedValueTypeException($value);
    }

    /**
     * @param array<integer,mixed> &$value
     *
     * @return Value\ValueInterface
     */
    protected function createArray(array &$value)
    {
        $size = count($value);
        if ($size > 0 && array_keys($value) !== range(0, $size - 1)) {
            return $this->createObjectFromArray($value);
        }

        $instance = new Value\ArrayValue;
        $this->register($value, $instance);

        foreach ($value as $key => &$subValue) {
            $instance->set($key, $this->createValue($subValue));
        }

        return $instance;
    }

    /**
     * @param array &$value
     *
     * @return Value\ValueInterface
     */
    protected function createObjectFromArray(array &$value)
    {
        if (array_key_exists('$ref', $value) && is_string($value['$ref'])) {
            return $this->createReferenceFromArray($value);
        }

        $instance = new Value\ObjectValue;
        $this->register($value, $instance);

        foreach ($value as $key => &$subValue) {
            $instance->set($key, $this->createValue($subValue));
        }

        return $instance;
    }

    /**
     * @param array &$value
     *
     * @return Value\ValueInterface
     */
    protected function createReferenceFromArray(array &$value)
    {
        $uri = new Uri($value['$ref']);
        $mimeType = null;
        if (array_key_exists('$type', $value) && is_string($value['$type'])) {
            $mimeType = $value['$type'];
        }

        return $this->register(
            $value,
            new Value\ReferenceValue($uri, $mimeType)
        );
    }

    /**
     * @param stdClass &$value
     *
     * @return Value\ValueInterface
     */
    protected function createObject(stdClass &$value)
    {
        if (property_exists($value, '$ref') && is_string($value->{'$ref'})) {
            return $this->createReferenceFromObject($value);
        }

        $instance = new Value\ObjectValue;
        $this->register($value, $instance);

        foreach (get_object_vars($value) as $property => $subValue) {
            $realProperty = $property;
            if ('_empty_' === $property) {
                $property = '';
            }

            $instance->set($property, $this->createValue($value->$realProperty));
        }

        return $instance;
    }

    /**
     * @param stdClass &$value
     *
     * @return Value\ValueInterface
     */
    protected function createReferenceFromObject(stdClass &$value)
    {
        $uri = new Uri($value->{'$ref'});
        $mimeType = null;
        if (property_exists($value, '$type') && is_string($value->{'$type'})) {
            $mimeType = $value->{'$type'};
        }

        return $this->register(
            $value,
            new Value\ReferenceValue($uri, $mimeType)
        );
    }

    protected function clear()
    {
        $this->instances = array();
    }

    /**
     * @param mixed                &$value
     * @param Value\ValueInterface $instance
     *
     * @return Value\ValueInterface
     */
    protected function register(&$value, $instance)
    {
        $this->instances[] = array(&$value, $instance);

        return $instance;
    }

    /**
     * @param mixed &$value
     *
     * @return Value\ValueInterface|null
     */
    protected function instance(&$value)
    {
        foreach ($this->instances as &$tuple) {
            if ($tuple[0] === $value) {
                if (
                    is_array($value) &&
                    !$this->isArrayReference($tuple[0], $value)
                ) {
                    continue;
                }

                return $tuple[1];
            }
        }

        return null;
    }

    /**
     * @param array &$left
     * @param array &$right
     *
     * @return boolean
     */
    protected function isArrayReference(array &$left, array &$right)
    {
        $id = uniqid('schemer-', true);
        $left[$id] = true;
        $isArrayReference = array_key_exists($id, $right);
        unset($left[$id]);

        return $isArrayReference;
    }

    private $instances;
}
