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

use stdClass;

class ObjectValue extends AbstractObjectValue
{
    /**
     * @param stdClass|null $value
     */
    public function __construct(stdClass $value = null)
    {
        if (null === $value) {
            $value = new stdClass;
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
        return $visitor->visitObjectValue($this);
    }
}
