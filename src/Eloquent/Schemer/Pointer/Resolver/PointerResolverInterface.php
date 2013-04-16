<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Resolver;

use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Value\ValueInterface;

interface PointerResolverInterface
{
    /**
     * @param PointerInterface $pointer
     * @param ValueInterface   $value
     *
     * @return ValueInterface|null
     */
    public function resolve(PointerInterface $pointer, ValueInterface $value);
}
