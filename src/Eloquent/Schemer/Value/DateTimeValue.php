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

class DateTimeValue extends AbstractConcreteValue
{
    /**
     * @param DateTime $value
     */
    public function __construct(DateTime $value)
    {
        parent::__construct($value);
    }

    /**
     * @return ValueType
     */
    public function valueType()
    {
        return ValueType::DATE_TIME_TYPE();
    }

    /**
     * @param Visitor\ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(Visitor\ValueVisitorInterface $visitor)
    {
        return $visitor->visitDateTimeValue($this);
    }

    /**
     * @param array<tuple<string,mixed>> &$valueMap
     *
     * @return mixed
     */
    protected function unwrap(array &$valueMap)
    {
        return clone $this->value;
    }
}
