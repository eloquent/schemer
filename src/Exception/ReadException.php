<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Exception;

use Exception;

/**
 * Unable to read from a file or stream.
 */
final class ReadException extends AbstractIoException
{
    /**
     * Construct a new read exception.
     *
     * @param string|null    $path  The path, if available.
     * @param Exception|null $cause The cause, if available.
     */
    public function __construct($path = null, Exception $cause = null)
    {
        if (null === $path) {
            $message = 'Unable to read from stream.';
        } else {
            $message =
                sprintf('Unable to read from %s.', var_export($path, true));
        }

        parent::__construct($message, $path, $cause);
    }
}
