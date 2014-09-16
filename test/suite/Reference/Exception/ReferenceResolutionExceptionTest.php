<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class ReferenceResolutionExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $baseUri = 'baseUri';
        $uri = 'uri';
        $cause = new Exception;
        $exception = new ReferenceResolutionException($baseUri, $uri, $cause);

        $this->assertSame($baseUri, $exception->baseUri());
        $this->assertSame($uri, $exception->uri());
        $this->assertSame("Unable to resolve reference 'uri' from context 'baseUri'.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
