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

use Eloquent\Enumeration\Enumeration;

final class ValueType extends Enumeration
{
    const ARRAY_TYPE = 'array';
    const BOOLEAN_TYPE = 'boolean';
    const DATETIME_TYPE = 'datetime';
    const INTEGER_TYPE = 'integer';
    const NULL_TYPE = 'null';
    const NUMBER_TYPE = 'number';
    const OBJECT_TYPE = 'object';
    const STRING_TYPE = 'string';
}
