<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer;

use Eloquent\Schemer\Uri\UriInterface;

interface PointerFactoryInterface
{
    /**
     * @param string|null $pointer
     *
     * @return PointerInterface
     */
    public function create($pointer = null);

    /**
     * @param UriInterface $uri
     *
     * @return PointerInterface
     */
    public function createFromUri(UriInterface $uri);
}
