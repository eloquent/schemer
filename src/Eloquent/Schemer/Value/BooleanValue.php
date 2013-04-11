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

use InvalidArgumentException;

class BooleanValue extends AbstractValue
{
    /**
     * @param boolean $value
     */
    public function __construct($value)
    {
        if (!is_bool($value)) {
            throw new InvalidArgumentException('Value must be a boolean.');
        }

        parent::__construct($value);
    }

    /**
     * @return ValueType
     */
    public function valueType()
    {
        return ValueType::BOOLEAN_TYPE();
    }

    /**
     * @param ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ValueVisitorInterface $visitor)
    {
        return $visitor->visitBooleanValue($this);
    }
}
