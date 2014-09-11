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
 * An error occurred while attempting to serialize or unserialize JSON data.
 */
final class JsonException extends Exception
{
    /**
     * Construct a new JSON exception.
     *
     * @param integer        $error The JSON error.
     * @param Exception|null $cause The cause, if available.
     */
    public function __construct($error, Exception $cause = null)
    {
        if (defined('JSON_ERROR_RECURSION')) {
            $jsonErrorRecursion = JSON_ERROR_RECURSION;
        } else { // @codeCoverageIgnoreStart
            $jsonErrorRecursion = 'JSON_ERROR_RECURSION';
        } // @codeCoverageIgnoreEnd

        if (defined('JSON_ERROR_INF_OR_NAN')) {
            $jsonErrorInfOrNan = JSON_ERROR_INF_OR_NAN;
        } else { // @codeCoverageIgnoreStart
            $jsonErrorInfOrNan = 'JSON_ERROR_INF_OR_NAN';
        } // @codeCoverageIgnoreEnd

        if (defined('JSON_ERROR_UNSUPPORTED_TYPE')) {
            $jsonErrorUnsupportedType = JSON_ERROR_UNSUPPORTED_TYPE;
        } else { // @codeCoverageIgnoreStart
            $jsonErrorUnsupportedType = 'JSON_ERROR_UNSUPPORTED_TYPE';
        } // @codeCoverageIgnoreEnd

        switch ($error) {
            case JSON_ERROR_DEPTH:
                $message = 'The maximum stack depth has been exceeded.';
                break;

            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Invalid or malformed JSON.';
                break;

            case JSON_ERROR_CTRL_CHAR:
                $message =
                    'Control character error, possibly incorrectly encoded.';
                break;

            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error.';
                break;

            case JSON_ERROR_UTF8:
                $message =
                    'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;

            case $jsonErrorRecursion:
                $message = 'One or more recursive references in the value to ' .
                    'be encoded.';
                break;

            case $jsonErrorInfOrNan:
                $message =
                    'One or more NAN or INF values in the value to be encoded.';
                break;

            case $jsonErrorUnsupportedType:
                $message =
                    'A value of a type that cannot be encoded was given.';
                break;

            default:
                $message = 'Unknown error.';
        }

        parent::__construct(
            sprintf('JSON error: %s', $message),
            $error,
            $cause
        );
    }
}
