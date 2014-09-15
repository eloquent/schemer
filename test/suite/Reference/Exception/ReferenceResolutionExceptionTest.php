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
        $referenceUri = 'path/to/reference';
        $contextUri = 'file:///path/to/directory';
        $cause = new Exception;
        $exception = new ReferenceResolutionException($referenceUri, $contextUri, $cause);

        $this->assertSame($referenceUri, $exception->referenceUri());
        $this->assertSame($contextUri, $exception->contextUri());
        $this->assertSame(
            "Unable to resolve reference 'path/to/reference' from context 'file:///path/to/directory'.",
            $exception->getMessage()
        );
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
