<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri\Resolution\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class UriResolutionExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $uri = 'uri';
        $baseUri = 'baseUri';
        $cause = new Exception;
        $exception = new UriResolutionException($uri, $baseUri, $cause);

        $this->assertSame($uri, $exception->uri());
        $this->assertSame($baseUri, $exception->baseUri());
        $this->assertSame("Unable to resolve URI 'uri' against base URI 'baseUri'.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
