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

class ArrayValue extends AbstractValue
{
    /**
     * @param array<integer,mixed> $value
     */
    public function __construct(array $value)
    {
        $expectedIndex = 0;
        foreach ($value as $index => $subValue) {
            if ($index !== $expectedIndex++) {
                throw new InvalidArgumentException(
                    'Value must be a sequential array.'
                );
            }
            if (!$subValue instanceof ValueInterface) {
                throw new InvalidArgumentException(
                    'Value must contain only instances of ValueInterface.'
                );
            }
        }

        parent::__construct($value);
    }

    /**
     * @param ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ValueVisitorInterface $visitor)
    {
        return $visitor->visitArrayValue($this);
    }
}
