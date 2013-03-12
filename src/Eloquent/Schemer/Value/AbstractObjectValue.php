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
use stdClass;

abstract class AbstractObjectValue extends AbstractValue
{
    /**
     * @param stdClass $value
     */
    public function __construct(stdClass $value)
    {
        foreach (get_object_vars($value) as $key => $subValue) {
            if (!$subValue instanceof ValueInterface) {
                throw new InvalidArgumentException(
                    'Value must contain only instances of ValueInterface.'
                );
            }
        }

        parent::__construct($value);
    }
}
