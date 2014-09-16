<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference;

use Eloquent\Schemer\Reference\Exception\ReferenceResolutionException;

/**
 * The interface implemented by reference resolvers.
 */
interface ReferenceResolverInterface
{
    /**
     * Resolve all references in the supplied value.
     *
     * @param mixed $value The value to resolve.
     *
     * @return mixed                        The resolved value.
     * @throws ReferenceResolutionException If the value cannot be resolved.
     */
    public function resolve($value);

    /**
     * Resolve a value as specified by a URI.
     *
     * @param string $baseUri The base URI.
     * @param string $uri     The URI reference.
     *
     * @return mixed                        The resolved value.
     * @throws ReferenceResolutionException If the value cannot be resolved.
     */
    public function resolveUri($baseUri, $uri);
}
