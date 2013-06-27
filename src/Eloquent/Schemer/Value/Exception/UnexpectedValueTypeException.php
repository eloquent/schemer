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

final class UnexpectedValueTypeException extends Exception
{
    /**
     * @param mixed          $value
     * @param string         $expectedType
     * @param Exception|null $previous
     */
    public function __construct(
        $value,
        $expectedType,
        Exception $previous = null
    ) {
        $this->value = $value;
        $this->expectedType = $expectedType;

        parent::__construct(
            sprintf(
                'Unexpected value of type %s. Value must be of type %s.',
                var_export(gettype($value), true),
                $expectedType
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

    /**
     * @return string
     */
    public function expectedType()
    {
        return $this->expectedType;
    }

    private $value;
    private $expectedType;
}
