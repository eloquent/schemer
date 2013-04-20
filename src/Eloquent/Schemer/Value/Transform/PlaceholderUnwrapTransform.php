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

use Eloquent\Schemer\Value;

class PlaceholderUnwrapTransform extends AbstractValueTransform
{
    /**
     * @param Value\PlaceholderValue $value
     *
     * @return Value\ValueInterface
     */
    public function visitPlaceholderValue(Value\PlaceholderValue $value)
    {
        if ($value->isClosedRecursion()) {
            $nullValue = new Value\NullValue;
            $value->innerValue()->setInnerValue($nullValue);

            return $nullValue;
        }

        while ($value instanceof Value\PlaceholderValue) {
            $value = $value->innerValue();
        }

        return $value;
    }
}
