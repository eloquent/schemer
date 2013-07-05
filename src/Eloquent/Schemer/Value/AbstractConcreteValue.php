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

abstract class AbstractConcreteValue implements ConcreteValueInterface
{
    /**
     * @param mixed $value
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        $valueMap = array();

        return $this->unwrap($valueMap);
    }

    /**
     * @param array<tuple<string,mixed>> &$valueMap
     *
     * @return mixed
     */
    protected function unwrap(array &$valueMap)
    {
        return $this->value;
    }

    /**
     * @param ValueInterface $value
     */
    protected function valueTypeCheck(ValueInterface $value)
    {
    }

    protected $value;
}
