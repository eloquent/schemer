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

use stdClass;

class AssociativeValueTransform extends ValueTransform
{
    /**
     * @param array<integer,mixed> $value
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    protected function transformArray(array $value)
    {
        if (array_keys($value) !== range(0, count($value) - 1)) {
            $object = new stdClass;
            foreach ($value as $key => $subValue) {
                $object->$key = $subValue;
            }

            return $this->transformObject($object);
        }

        return parent::transformArray($value);
    }
}
