<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Factory\Exception;

use Exception;

/**
 * The supplied pointer string is invalid.
 */
final class InvalidPointerException extends Exception
{
    /**
     * Construct a new invalid pointer exception.
     *
     * @param string         $pointer The pointer.
     * @param Exception|null $cause   The cause, if available.
     */
    public function __construct($pointer, Exception $cause = null)
    {
        $this->pointer = $pointer;

        parent::__construct(
            sprintf('Invalid pointer %s.', var_export($pointer, true)),
            0,
            $cause
        );
    }

    /**
     * Get the pointer.
     *
     * @return string The pointer.
     */
    public function pointer()
    {
        return $this->pointer;
    }

    private $pointer;
}
