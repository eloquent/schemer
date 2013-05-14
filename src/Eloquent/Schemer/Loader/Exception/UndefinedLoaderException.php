<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Loader\Exception;

use Exception;
use LogicException;

final class UndefinedLoaderException extends LogicException
{
    /**
     * @param string         $scheme
     * @param Exception|null $previous
     */
    public function __construct($scheme, Exception $previous = null)
    {
        $this->scheme = $scheme;

        parent::__construct(
            sprintf(
                "No loader defined for scheme %s.",
                var_export($scheme, true)
            ),
            0,
            $previous
        );
    }

    /**
     * @return string
     */
    public function scheme()
    {
        return $this->scheme;
    }

    private $scheme;
}
