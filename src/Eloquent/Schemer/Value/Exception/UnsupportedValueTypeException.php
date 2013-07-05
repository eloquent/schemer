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

final class UnsupportedValueTypeException extends Exception
{
    /**
     * @param mixed          $value
     * @param Exception|null $previous
     */
    public function __construct($value, Exception $previous = null)
    {
        $this->value = $value;

        parent::__construct(
            sprintf(
                'Values of type %s are not supported.',
                var_export(gettype($value), true)
            ),
            0,
            $previous
        );
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    private $value;
}
