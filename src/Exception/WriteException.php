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
 * Unable to write to a file or stream.
 */
final class WriteException extends AbstractIoException
{
    /**
     * Construct a new write exception.
     *
     * @param string|null    $path  The path, if available.
     * @param Exception|null $cause The cause, if available.
     */
    public function __construct($path = null, Exception $cause = null)
    {
        if (null === $path) {
            $message = 'Unable to write to stream.';
        } else {
            $message =
                sprintf('Unable to write to %s.', var_export($path, true));
        }

        parent::__construct($message, $path, $cause);
    }
}
