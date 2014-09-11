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

class ReadExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $location = 'location';
        $cause = new Exception;
        $exception = new ReadException($location, $cause);

        $this->assertSame($location, $exception->location());
        $this->assertSame("Unable to read from 'location'.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }

    public function testExceptionDefaults()
    {
        $exception = new ReadException;

        $this->assertNull($exception->location());
        $this->assertSame("Unable to read from stream.", $exception->getMessage());
    }
}
