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

class SerializeExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $value = 'value';
        $cause = new Exception;
        $exception = new SerializeException($value, $cause);

        $this->assertSame($value, $exception->value());
        $this->assertSame("Unable to serialize value.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
