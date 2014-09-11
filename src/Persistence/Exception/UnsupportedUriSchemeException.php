<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Persistence\Exception;

use Exception;

/**
 * The supplied URI scheme is not supported.
 */
final class UnsupportedUriSchemeException extends Exception
{
    /**
     * Construct a new unsupported URI scheme exception.
     *
     * @param string         $scheme The scheme.
     * @param Exception|null $cause  The cause, if available.
     */
    public function __construct($scheme, Exception $cause = null)
    {
        $this->scheme = $scheme;

        parent::__construct(
            sprintf('Unsupported URI scheme %s.', var_export($scheme, true)),
            0,
            $cause
        );
    }

    /**
     * Get the scheme.
     *
     * @return string The scheme.
     */
    public function scheme()
    {
        return $this->scheme;
    }

    private $scheme;
}
