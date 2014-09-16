<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri\Resolution\Exception;

use Exception;

/**
 * The URI reference could not be resolved against the base URI.
 */
final class UriResolutionException extends Exception
{
    /**
     * Construct a new URI resolution exception.
     *
     * @param string         $uri     The URI reference.
     * @param string         $baseUri The base URI.
     * @param Exception|null $cause   The cause, if available.
     */
    public function __construct($uri, $baseUri, Exception $cause = null)
    {
        $this->uri = $uri;
        $this->baseUri = $baseUri;

        parent::__construct(
            sprintf(
                'Unable to resolve URI %s against base URI %s.',
                var_export($uri, true),
                var_export($baseUri, true)
            ),
            0,
            $cause
        );
    }

    /**
     * Get the URI reference.
     *
     * @return string The URI reference.
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Get the base URI.
     *
     * @return string The base URI.
     */
    public function baseUri()
    {
        return $this->baseUri;
    }

    private $uri;
    private $baseUri;
}
