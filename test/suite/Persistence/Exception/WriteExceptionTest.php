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

class WriteExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $location = 'location';
        $cause = new Exception;
        $exception = new WriteException($location, $cause);

        $this->assertSame($location, $exception->location());
        $this->assertSame("Unable to write to 'location'.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }

    public function testExceptionDefaults()
    {
        $exception = new WriteException;

        $this->assertNull($exception->location());
        $this->assertSame("Unable to write to stream.", $exception->getMessage());
    }
}
