<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Constraint\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class InvalidSchemaSpecificationExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $specification = 'specification';
        $cause = new Exception;
        $exception = new InvalidSchemaSpecificationException($specification, $cause);

        $this->assertSame($specification, $exception->specification());
        $this->assertSame("The supplied schema specification is invalid.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
