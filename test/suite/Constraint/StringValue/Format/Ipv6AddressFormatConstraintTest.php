<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\StringValue\Format;

use Phake;
use PHPUnit_Framework_TestCase;

class Ipv6AddressFormatConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->constraint = new Ipv6AddressFormatConstraint;
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitIpv6AddressFormatConstraint($this->constraint);
    }
}
