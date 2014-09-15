<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Resolver;

use Eloquent\Schemer\Pointer\PointerInterface;

/**
 * The interface implemented by bound pointer resolvers.
 */
interface BoundPointerResolverInterface
{
    /**
     * Resolve a pointer within the value tree.
     *
     * @param PointerInterface $pointer The pointer.
     *
     * @return tuple<mixed,boolean> A 2-tuple containing the resolved value if successful, and a boolean indicating success.
     */
    public function resolve(PointerInterface $pointer);
}
