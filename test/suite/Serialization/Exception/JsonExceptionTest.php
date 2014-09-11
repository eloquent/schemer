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
use PHPUnit_Framework_TestCase;

class JsonExceptionTest extends PHPUnit_Framework_TestCase
{
    public function exceptionData()
    {
        //                                 error                          message
        return [
            'Unknown error'            => ['JSON_ERROR_NONE',             "JSON error: Unknown error."],
            'Depth'                    => ['JSON_ERROR_DEPTH',            "JSON error: The maximum stack depth has been exceeded."],
            'State mismatch'           => ['JSON_ERROR_STATE_MISMATCH',   "JSON error: Invalid or malformed JSON."],
            'Control character'        => ['JSON_ERROR_CTRL_CHAR',        "JSON error: Control character error, possibly incorrectly encoded."],
            'Syntax'                   => ['JSON_ERROR_SYNTAX',           "JSON error: Syntax error."],
            'UTF-8'                    => ['JSON_ERROR_UTF8',             "JSON error: Malformed UTF-8 characters, possibly incorrectly encoded."],
            'Recursion'                => ['JSON_ERROR_RECURSION',        "JSON error: One or more recursive references in the value to be encoded."],
            'Infinity or not a number' => ['JSON_ERROR_INF_OR_NAN',       "JSON error: One or more NAN or INF values in the value to be encoded."],
            'Unsupported type'         => ['JSON_ERROR_UNSUPPORTED_TYPE', "JSON error: A value of a type that cannot be encoded was given."],
        ];
    }

    /**
     * @dataProvider exceptionData
     */
    public function testException($error, $message)
    {
        if (!defined($error)) {
            $this->markTestSkipped(sprintf('%s is not defined', $error));
        }

        $error = constant($error);
        $cause = new Exception;
        $exception = new JsonException($error, $cause);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($error, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
