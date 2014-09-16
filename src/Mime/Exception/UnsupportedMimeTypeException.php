<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Mime\Exception;

use Exception;

/**
 * An unsupported MIME type was encountered.
 */
final class UnsupportedMimeTypeException extends Exception
{
    /**
     * Construct a new unsupported MIME type exception.
     *
     * @param string         $type  The MIME type.
     * @param Exception|null $cause The cause, if available.
     */
    public function __construct($type, Exception $cause = null)
    {
        $this->type = $type;

        parent::__construct(
            sprintf('Unsupported MIME type %s.', var_export($type, true)),
            0,
            $cause
        );
    }

    /**
     * Get the MIME type.
     *
     * @return string The MIME type.
     */
    public function type()
    {
        return $this->type;
    }

    private $type;
}
