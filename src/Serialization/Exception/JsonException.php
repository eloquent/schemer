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
        if (JSON_ERROR_DEPTH === $error) {
            $message = 'The maximum stack depth has been exceeded.';
        } elseif (JSON_ERROR_STATE_MISMATCH === $error) {
            $message = 'Invalid or malformed JSON.';
        } elseif (JSON_ERROR_CTRL_CHAR === $error) {
            $message = 'Control character error, possibly incorrectly encoded.';
        } elseif (JSON_ERROR_SYNTAX === $error) {
            $message = 'Syntax error.';
        } elseif (JSON_ERROR_UTF8 === $error) {
            $message =
                'Malformed UTF-8 characters, possibly incorrectly encoded.';
        } elseif (
            defined('JSON_ERROR_RECURSION') &&
            constant('JSON_ERROR_RECURSION') === $error
        ) {
            $message =
                'One or more recursive references in the value to be encoded.';
        } elseif (
            defined('JSON_ERROR_INF_OR_NAN') &&
            constant('JSON_ERROR_INF_OR_NAN') === $error
        ) {
            $message =
                'One or more NAN or INF values in the value to be encoded.';
        } elseif (
            defined('JSON_ERROR_UNSUPPORTED_TYPE') &&
            constant('JSON_ERROR_UNSUPPORTED_TYPE') === $error
        ) {
            $message = 'A value of a type that cannot be encoded was given.';
        } else {
            $message = 'Unknown error.';
        }

        parent::__construct(
            sprintf('JSON error: %s', $message),
            $error,
            $cause
        );
    }
}
