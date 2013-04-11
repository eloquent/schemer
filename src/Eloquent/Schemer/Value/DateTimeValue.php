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

use DateTime;

class DateTimeValue extends AbstractValue
{
    /**
     * @param DateTime $value
     */
    public function __construct(DateTime $value)
    {
        parent::__construct($value);
    }

    /**
     * @return DateTime
     */
    public function value()
    {
        return clone $this->wrappedValue();
    }

    /**
     * @return ValueType
     */
    public function valueType()
    {
        return ValueType::DATETIME_TYPE();
    }

    /**
     * @param ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ValueVisitorInterface $visitor)
    {
        return $visitor->visitDateTimeValue($this);
    }
}
