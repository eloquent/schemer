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

use Eloquent\Schemer\Value;
use ReflectionClass;

class PlaceholderUnwrapTransform extends AbstractValueTransform
{
    public function __construct()
    {
        parent::__construct();

        $this->reflector = new ReflectionClass(
            'Eloquent\Schemer\Value\AbstractValue'
        );
    }

    /**
     * @param Value\ArrayValue $value
     *
     * @return Value\ArrayValue
     */
    public function visitArrayValue(Value\ArrayValue $value)
    {
        if ($this->isVisited($value)) {
            return $value;
        }
        $this->setVisited($value);

        $valueProperty = $this->reflector->getProperty('value');
        $valueProperty->setAccessible(true);
        $innerValue = $valueProperty->getValue($value);
        foreach ($innerValue as $index => $subValue) {
            if ($subValue instanceof Value\PlaceholderValue) {
                $innerValue[$index] = $innerValue[$index]->accept($this);
                $valueProperty->setValue($innerValue);
            }
            $innerValue[$index] = $innerValue[$index]->accept($this);
            $valueProperty->setValue($innerValue);
        }

        return $value;
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return Value\ObjectValue
     */
    public function visitObjectValue(Value\ObjectValue $value)
    {
        if ($this->isVisited($value)) {
            return $value;
        }
        $this->setVisited($value);

        $valueProperty = $this->reflector->getProperty('value');
        $valueProperty->setAccessible(true);
        $innerValue = $valueProperty->getValue($value);
        foreach (get_object_vars($innerValue) as $property => $subValue) {
            if ($subValue instanceof Value\PlaceholderValue) {
                $innerValue->$property = $innerValue->$property->accept($this);
            }
            $innerValue->$property = $innerValue->$property->accept($this);
        }

        return $value;
    }

    /**
     * @param Value\PlaceholderValue $value
     *
     * @return Value\ValueInterface
     */
    public function visitPlaceholderValue(Value\PlaceholderValue $value)
    {
        if ($value->isClosedRecursion()) {
            $nullValue = new Value\NullValue;
            $value->innerValue()->setInnerValue($nullValue);

            return $nullValue;
        }

        while ($value instanceof Value\PlaceholderValue) {
            $value = $value->innerValue();
        }

        return $value;
    }

    protected function clear()
    {
        parent::clear();

        $this->visited = array();
    }

    /**
     * @param Value\ValueInterface $value
     */
    protected function setVisited(Value\ValueInterface $value)
    {
        $this->visited[spl_object_hash($value)] = true;
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return boolean
     */
    protected function isVisited(Value\ValueInterface $value)
    {
        return array_key_exists(spl_object_hash($value), $this->visited);
    }

    private $reflector;
    private $visited;
}
