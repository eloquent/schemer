<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\StringValue;

use Phake;
use PHPUnit_Framework_TestCase;

class MaximumLengthConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->maximum = 111;
        $this->constraint = new MaximumLengthConstraint($this->maximum);
    }

    public function testConstructor()
    {
        $this->assertSame($this->maximum, $this->constraint->maximum());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitMaximumLengthConstraint($this->constraint);
    }
}
