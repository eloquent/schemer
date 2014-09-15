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
use PHPUnit_Framework_TestCase;

class InvalidUriExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $uri = 'uri';
        $cause = new Exception;
        $exception = new InvalidUriException($uri, $cause);

        $this->assertSame($uri, $exception->uri());
        $this->assertSame("Invalid URI 'uri'.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
