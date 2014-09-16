<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Mime\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class UnsupportedMimeTypeExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $type = 'type';
        $cause = new Exception;
        $exception = new UnsupportedMimeTypeException($type, $cause);

        $this->assertSame($type, $exception->type());
        $this->assertSame("Unsupported MIME type 'type'.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
