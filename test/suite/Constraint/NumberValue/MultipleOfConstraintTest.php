<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\NumberValue;

use Phake;
use PHPUnit_Framework_TestCase;

class MultipleOfConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->quantity = 111;
        $this->constraint = new MultipleOfConstraint($this->quantity);
    }

    public function testConstructor()
    {
        $this->assertSame($this->quantity, $this->constraint->quantity());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitMultipleOfConstraint($this->constraint);
    }
}
