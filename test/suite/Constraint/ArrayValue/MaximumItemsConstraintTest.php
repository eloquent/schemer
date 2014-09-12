<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ArrayValue;

use Phake;
use PHPUnit_Framework_TestCase;

class MaximumItemsConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->maximum = 111;
        $this->constraint = new MaximumItemsConstraint($this->maximum);
    }

    public function testConstructor()
    {
        $this->assertSame($this->maximum, $this->constraint->maximum());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitMaximumItemsConstraint($this->constraint);
    }
}
