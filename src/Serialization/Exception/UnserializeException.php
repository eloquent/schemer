<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Exception;

use Exception;

/**
 * The supplied data could not be unserialized.
 */
final class UnserializeException extends Exception
{
    /**
     * Construct a new unserialize exception.
     *
     * @param string         $data  The data.
     * @param Exception|null $cause The cause, if available.
     */
    public function __construct($data, Exception $cause = null)
    {
        $this->data = $data;

        parent::__construct('Unable to unserialize data.', 0, $cause);
    }

    /**
     * Get the data.
     *
     * @return string The data.
     */
    public function data()
    {
        return $this->data;
    }

    private $data;
}
