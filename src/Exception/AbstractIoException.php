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
 * An abstract base class for implementing I/O exceptions.
 */
abstract class AbstractIoException extends Exception implements
    IoExceptionInterface
{
    /**
     * Construct a new I/O exception.
     *
     * @param string         $message The exception message.
     * @param string|null    $path    The path, if available.
     * @param Exception|null $cause   The cause, if available.
     */
    public function __construct($message, $path = null, Exception $cause = null)
    {
        $this->path = $path;

        parent::__construct($message, 0, $cause);
    }

    /**
     * Get the path.
     *
     * @return string|null The path, if available.
     */
    public function path()
    {
        return $this->path;
    }

    private $path;
}
