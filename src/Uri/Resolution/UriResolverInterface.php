<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri\Resolution;

use Eloquent\Schemer\Uri\Resolution\Exception\UriResolutionException;

/**
 * The interface implemented by URI resolvers.
 */
interface UriResolverInterface
{
    /**
     * Resolve a URI reference against a base URI.
     *
     * @param string $baseUri The base URI.
     * @param string $uri     The URI reference.
     *
     * @return string                 The resolved URI.
     * @throws UriResolutionException If the URI reference cannot be resolved.
     */
    public function resolve($baseUri, $uri);
}
