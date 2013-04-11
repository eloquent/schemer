<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Exception;

use Exception;
use LogicException;

final class UndefinedProtocolException extends LogicException
{
    /**
     * @param string         $mimeType
     * @param Exception|null $previous
     */
    public function __construct($mimeType, Exception $previous = null)
    {
        $this->mimeType = $mimeType;

        parent::__construct(
            sprintf("No serialization protocol defined for MIME type '%s'.", $mimeType),
            0,
            $previous
        );
    }

    /**
     * @return string
     */
    public function mimeType()
    {
        return $this->mimeType;
    }

    private $mimeType;
}
