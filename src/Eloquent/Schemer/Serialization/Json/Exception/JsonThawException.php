<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Json\Exception;

use Eloquent\Schemer\Serialization\Exception\ThawExceptionInterface;
use Exception;

final class JsonThawException extends Exception implements ThawExceptionInterface
{
    /**
     * @param integer        $jsonErrorCode
     * @param Exception|null $previous
     */
    public function __construct($jsonErrorCode, Exception $previous = null)
    {
        switch ($jsonErrorCode) {
            case JSON_ERROR_DEPTH:
                $message = 'Maximum stack depth exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Underflow or mode mismatch.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = 'Unexpected control character or encoding issue.';
                break;
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error.';
                break;
            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            default:
                $message = 'Unknown error.';
        }

        $this->jsonErrorCode = $jsonErrorCode;

        parent::__construct(
            sprintf('Unable to thaw JSON data. %s', $message),
            0,
            $previous
        );
    }

    /**
     * @return integer
     */
    public function jsonErrorCode()
    {
        return $this->jsonErrorCode;
    }

    private $jsonErrorCode;
}
