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

class NullValue extends AbstractValue
{
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @return ValueType
     */
    public function valueType()
    {
        return ValueType::NULL_TYPE();
    }

    /**
     * @param ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ValueVisitorInterface $visitor)
    {
        return $visitor->visitNullValue($this);
    }
}
