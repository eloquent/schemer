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

class MinimumItemsConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->minimum = 111;
        $this->constraint = new MinimumItemsConstraint($this->minimum);
    }

    public function testConstructor()
    {
        $this->assertSame($this->minimum, $this->constraint->minimum());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitMinimumItemsConstraint($this->constraint);
    }
}
