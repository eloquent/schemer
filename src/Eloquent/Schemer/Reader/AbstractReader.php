<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reader;

use Eloquent\Schemer\Value\Transform\ValueTransform;
use Eloquent\Schemer\Value\Transform\ValueTransformInterface;

abstract class AbstractReader implements ReaderInterface
{
    /**
     * @param ValueTransformInterface|null $transform
     */
    public function __construct(ValueTransformInterface $transform = null)
    {
        if (null === $transform) {
            $transform = new ValueTransform;
        }

        $this->transform = $transform;
    }

    /**
     * @return ValueTransformInterface
     */
    public function transform()
    {
        return $this->transform;
    }

    private $transform;
}
