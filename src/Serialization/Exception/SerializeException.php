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
 * The supplied value could not be serialized.
 */
final class SerializeException extends Exception
{
    /**
     * Construct a new serialize exception.
     *
     * @param mixed          $value The value.
     * @param Exception|null $cause The cause, if available.
     */
    public function __construct($value, Exception $cause = null)
    {
        $this->value = $value;

        parent::__construct('Unable to serialize value.', 0, $cause);
    }

    /**
     * Get the value.
     *
     * @return mixed The value.
     */
    public function value()
    {
        return $this->value;
    }

    private $value;
}
