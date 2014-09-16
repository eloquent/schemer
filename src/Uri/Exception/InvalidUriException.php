<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri\Exception;

use Exception;

/**
 * The supplied URI is invalid.
 */
final class InvalidUriException extends Exception
{
    /**
     * Construct a new invalid URI exception.
     *
     * @param string         $uri   The URI.
     * @param Exception|null $cause The cause, if available.
     */
    public function __construct($uri, Exception $cause = null)
    {
        $this->uri = $uri;

        parent::__construct(
            sprintf('Invalid URI %s.', var_export($uri, true)),
            0,
            $cause
        );
    }

    /**
     * Get the URI.
     *
     * @return string The URI.
     */
    public function uri()
    {
        return $this->uri;
    }

    private $uri;
}
