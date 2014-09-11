<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Persistence\Exception;

use Exception;
use GuzzleHttp\Message\Response;
use PHPUnit_Framework_TestCase;

class UnexpectedHttpResponseExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $response = new Response(444, array(), null, array('reason_phrase' => 'You done goofed'));
        $cause = new Exception;
        $exception = new UnexpectedHttpResponseException($response, $cause);

        $this->assertSame($response, $exception->response());
        $this->assertSame("Unexpected HTTP response: 'You done goofed' (444).", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
