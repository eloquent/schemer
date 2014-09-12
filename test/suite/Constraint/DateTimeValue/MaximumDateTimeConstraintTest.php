<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\DateTimeValue;

use DateTime;
use PHPUnit_Framework_TestCase;
use Phake;

class MaximumDateTimeConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->maximum = new DateTime;
        $this->constraint = new MaximumDateTimeConstraint($this->maximum, true);
    }

    public function testConstructor()
    {
        $this->assertSame($this->maximum, $this->constraint->maximum());
        $this->assertTrue($this->constraint->isExclusive());
    }

    public function testConstructorDefaults()
    {
        $this->constraint = new MaximumDateTimeConstraint($this->maximum);

        $this->assertFalse($this->constraint->isExclusive());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitMaximumDateTimeConstraint($this->constraint);
    }
}
