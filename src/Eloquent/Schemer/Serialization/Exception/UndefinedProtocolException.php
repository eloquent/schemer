<?php

/*
 * This file is part of the typer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\typer\Serialization\Exception;

use Exception;
use LogicException;

final class UndefinedProtocolException extends LogicException
{
    /**
     * @param string         $type
     * @param Exception|null $previous
     */
    public function __construct($type, Exception $previous = null)
    {
        $this->type = $type;

        parent::__construct(
            sprintf("No serialization protocol defined for type '%s'.", $type),
            0,
            $previous
        );
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    private $type;
}
