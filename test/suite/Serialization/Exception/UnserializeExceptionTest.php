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

class UnserializeExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $data = 'data';
        $cause = new Exception;
        $exception = new UnserializeException($data, $cause);

        $this->assertSame($data, $exception->data());
        $this->assertSame("Unable to unserialize data.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
