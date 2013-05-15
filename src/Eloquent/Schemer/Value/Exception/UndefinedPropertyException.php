<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value\Exception;

use Exception;

final class UndefinedPropertyException extends Exception
{
    /**
     * @param string         $pointer
     * @param Exception|null $previous
     */
    public function __construct($property, Exception $previous = null)
    {
        $this->property = $property;

        parent::__construct(
            sprintf("Undefined property: %s.", var_export($property, true)),
            0,
            $previous
        );
    }

    /**
     * @return string
     */
    public function property()
    {
        return $this->property;
    }

    private $property;
}
