<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Json;

use Eloquent\Schemer\Reader\ReaderInterface;

abstract class AbstractJsonReader implements ReaderInterface
{
    /**
     * @param JsonTransform|null $transform
     */
    public function __construct(JsonTransform $transform = null)
    {
        if (null === $transform) {
            $transform = new JsonTransform;
        }

        $this->transform = $transform;
    }

    /**
     * @return JsonTransform
     */
    public function transform()
    {
        return $this->transform;
    }

    private $transform;
}
