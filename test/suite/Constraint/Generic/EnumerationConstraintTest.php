<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Generic;

use Phake;
use PHPUnit_Framework_TestCase;

class EnumerationConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->members = ['foo', 'bar', 111];
        $this->constraint = new EnumerationConstraint($this->members);
    }

    public function testConstructor()
    {
        $this->assertSame($this->members, $this->constraint->members());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitEnumerationConstraint($this->constraint);
    }
}
