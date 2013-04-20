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

class PlaceholderUnwrapTransform extends AbstractValueTransform
{
    /**
     * @param Value\ArrayValue $value
     *
     * @return Value\ArrayValue
     */
    public function visitArrayValue(Value\ArrayValue $value)
    {
        return $this->transformValueContainer($value);
    }

    /**
     * @param Value\ObjectValue $value
     *
     * @return Value\ObjectValue
     */
    public function visitObjectValue(Value\ObjectValue $value)
    {
        return $this->transformValueContainer($value);
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

    /**
     * @param Value\ValueContainerInterface $value
     *
     * @return Value\ValueContainerInterface
     */
    protected function transformValueContainer(Value\ValueContainerInterface $value)
    {
        if ($this->isVisited($value)) {
            return $value;
        }
        $this->setVisited($value);

        foreach ($value->keys() as $key) {
            if ($value->get($key) instanceof Value\PlaceholderValue) {
                $value->set($key, $value->get($key)->accept($this));
            }
            $value->set($key, $value->get($key)->accept($this));
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
