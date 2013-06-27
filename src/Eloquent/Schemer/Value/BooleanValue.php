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

class BooleanValue extends AbstractConcreteValue
{
    /**
     * @param boolean $value
     */
    public function __construct($value)
    {
        if (!is_bool($value)) {
            throw new Exception\UnexpectedValueTypeException($value, 'boolean');
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
     * @param Visitor\ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(Visitor\ValueVisitorInterface $visitor)
    {
        return $visitor->visitBooleanValue($this);
    }
}
