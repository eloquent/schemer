<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference\Exception;

use Exception;

/**
 * Unable to resolve a reference.
 */
final class ReferenceResolutionException extends Exception
{
    /**
     * Construct a new reference resolution exception.
     *
     * @param string         $baseUri The base URI.
     * @param string         $uri     The URI reference.
     * @param Exception|null $cause   The cause, if available.
     */
    public function __construct($baseUri, $uri, Exception $cause = null)
    {
        $this->baseUri = $baseUri;
        $this->uri = $uri;

        parent::__construct(
            sprintf(
                'Unable to resolve reference %s from context %s.',
                var_export($uri, true),
                var_export($baseUri, true)
            ),
            0,
            $cause
        );
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

    /**
     * Get the URI reference.
     *
     * @return string The URI reference.
     */
    public function uri()
    {
        return $this->uri;
    }

    private $baseUri;
    private $uri;
}
