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
 * An abstract base class for implementing I/O exceptions.
 */
abstract class AbstractIoException extends Exception implements
    IoExceptionInterface
{
    /**
     * Construct a new I/O exception.
     *
     * @param string         $message  The exception message.
     * @param string|null    $location The location, if available.
     * @param Exception|null $cause    The cause, if available.
     */
    public function __construct(
        $message,
        $location = null,
        Exception $cause = null
    ) {
        $this->location = $location;

        parent::__construct($message, 0, $cause);
    }

    /**
     * Get the location.
     *
     * @return string|null The location, if available.
     */
    public function location()
    {
        return $this->location;
    }

    private $location;
}
