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

class MinimumDateTimeConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->minimum = new DateTime;
        $this->constraint = new MinimumDateTimeConstraint($this->minimum, true);
    }

    public function testConstructor()
    {
        $this->assertSame($this->minimum, $this->constraint->minimum());
        $this->assertTrue($this->constraint->isExclusive());
    }

    public function testConstructorDefaults()
    {
        $this->constraint = new MinimumDateTimeConstraint($this->minimum);

        $this->assertFalse($this->constraint->isExclusive());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitMinimumDateTimeConstraint($this->constraint);
    }
}
