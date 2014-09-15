<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Factory\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class InvalidPointerExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $pointer = 'pointer';
        $cause = new Exception;
        $exception = new InvalidPointerException($pointer, $cause);

        $this->assertSame($pointer, $exception->pointer());
        $this->assertSame("Invalid pointer 'pointer'.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
